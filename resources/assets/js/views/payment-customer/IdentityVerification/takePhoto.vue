<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80">
    <div v-if="!imageData">
      <sw-button variant="primary" type="button" @click="switchCamera" class="absolute top-0 right-0 m-4">
        <SwitchHorizontalIcon class="w-6 h-6" />
      </sw-button>
      <div class="camera-container">
        <video ref="video" @loadedmetadata="onVideoMetadataLoaded" autoplay playsinline class="video-element"></video>
        <canvas ref="canvas" class="hidden"></canvas>

        <div v-if="typePhoto === 'selfie'" class="overlayTakePhotoFace">
          <div class="circleFace" :class="{ 'no-face': !faceDetected }"></div>
          <div class="message" v-if="!faceDetected">No Face Detected</div>
        </div>

        <!-- rectángulo -->
        <div v-if="typePhoto === 'ic_front' || typePhoto === 'ic_back'" class="overlayTakePhotoRect">
          <div class="messageDocument">Center document on the screen</div>
        </div>
        <div class="camera-buttons">
          <sw-button variant="primary" type="button" @click="closeTakeModal">
            <XIcon class="w-6 h-6 mr-2" />
            {{ $t("general.cancel") }}
          </sw-button>
          <sw-button type="button" class="mx-4" @click="onCapture" :disabled="typePhoto === 'selfie' && !faceDetected">
            <CameraIcon class="w-6 h-6 mr-2" />
            Take
          </sw-button>
        </div>
      </div>
    </div>

    <div v-if="imageData">
      <img :src="imageData" alt="Foto tomada" />

      <div class="flex justify-center mt-5">
        <sw-button variant="primary" type="button" @click="reTakePhoto">
          <CameraIcon class="w-6 h-6" />
        </sw-button>

        <sw-button type="button" class="mx-4" @click="saveImage">
          <CheckIcon class="w-6 h-6" />
        </sw-button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from "vuex";
import { CameraIcon, CheckIcon, SwitchHorizontalIcon, XIcon } from "@vue-hero-icons/solid";

export default {
  components: {
    CameraIcon,
    XIcon,
    CheckIcon,
    SwitchHorizontalIcon,
  },
  props: {
    typePhoto: {
      type: String,
      default: null,
    },
    isShowTakeModal: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      imageData: null,
      devices: [],
      deviceSelect: {},
      faceDetected: false,
      stream: null,
    };
  },
  watch: {
    isShowTakeModal: {
      immediate: true,
      handler(value) {
        console.log("camera value", value);
        if (value) {
          this.initCamera();
        } else {
          this.stopCamera();
        }
      },
    },
  },
  methods: {
    async initCamera() {
      try {
        this.devices = await navigator.mediaDevices.enumerateDevices();
        console.log("Devices found:", this.devices);
        const videoDevices = this.devices.filter(device => device.kind === "videoinput");
        if (videoDevices.length > 0) {
          if (videoDevices.length > 1) {
            if (this.typePhoto === "selfie") {
              this.deviceSelect = videoDevices[0];
              this.startCamera(this.deviceSelect.deviceId);
            } else {
              this.deviceSelect = videoDevices[1];
              this.startCamera(this.deviceSelect.deviceId);
            }
          } else {
            this.deviceSelect = videoDevices[0];
            this.startCamera(this.deviceSelect.deviceId);
          }
        } else {
          alert("No video devices found");
        }
      } catch (error) {
        console.error("Error accessing camera: ", error);
        alert("Error accessing camera: " + error.message);
      }
    },
    async startCamera(deviceId) {
      if (!navigator?.mediaDevices || !navigator?.mediaDevices?.getUserMedia) {
        alert("getUserMedia is not supported in this browser");
        return;
      }

      try {
        const constraints = {
          video: {
            deviceId: deviceId ? { exact: deviceId } : undefined,
            facingMode: this.typePhoto === "selfie" ? "user" : "environment"
          },
          audio: false
        };
        this.stream = await navigator?.mediaDevices?.getUserMedia(constraints);
        console.log("Stream started:", this.stream);
        this.$refs.video.srcObject = this.stream;
        this.$refs.video.play();
      } catch (error) {
        console.error("Error starting camera: ", error);
        alert("Error starting camera: " + error.message);
      }
    },
    stopCamera() {
      if (this.stream) {
        this.stream.getTracks().forEach((track) => track.stop());
        this.stream = null;
      }
    },
    async onCapture() {
      const video = this.$refs.video;
      const canvas = this.$refs.canvas;
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      const context = canvas.getContext("2d");
      context.drawImage(video, 0, 0, canvas.width, canvas.height);
      this.imageData = canvas.toDataURL("image/png");

      this.stopCamera();
    },

    async reTakePhoto() {
      this.imageData = null;
      this.startCamera(this.deviceSelect.deviceId);
    },
    saveImage() {
      this.$emit("saveImage", this.imageData);
      this.closeTakeModal();
    },
    async closeTakeModal() {
      this.stopCamera();
      this.imageData = null;
      this.$emit("close");
    },
    async switchCamera() {
      const videoDevices = this.devices.filter(device => device.kind === "videoinput");
      const currentIndex = videoDevices.findIndex(device => device.deviceId === this.deviceSelect.deviceId);
      const nextIndex = (currentIndex + 1) % videoDevices.length;
      this.deviceSelect = videoDevices[nextIndex];
      this.stopCamera();
      this.startCamera(this.deviceSelect.deviceId);
    },
    async initTracking() {
      if (this.typePhoto !== "selfie") return;

      try {
        // Cargar face-api.js desde el CDN
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/gh/cgarciagl/face-api.js/dist/face-api.min.js';
        script.async = true;
        script.onload = async () => {
          // Cargar los modelos desde la CDN
          await faceapi.nets.ssdMobilenetv1.loadFromUri('https://cdn.jsdelivr.net/gh/cgarciagl/face-api.js/weights');
          const detectFace = async () => {
            try {
              const options = new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 });
              const result = await faceapi.detectSingleFace(this.$refs.video, options);
              this.faceDetected = !!result;
            } catch (error) {
              console.error("Error detecting face: ", error);
            }

            requestAnimationFrame(detectFace);
          };

          detectFace();
        };
        document.head.appendChild(script);
      } catch (error) {
        console.error("Error loading model or detecting face: ", error);
      }
    },
    async onVideoMetadataLoaded() {
      this.initTracking();
    }
  },
  beforeDestroy() {
    this.stopCamera();
  },
};
</script>

<style>
.camera-container {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.camera-buttons {
  position: absolute;
  margin-top: 0px;
  padding-top: 0px !important;
  margin-top: -50px;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
}

.video-element {
  width: 100%;
  height: 100%;
}

.overlayTakePhotoFace {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.circleFace {
  position: relative;
  width: 300px;
  height: 300px;
  border: 4px solid green;
  border-radius: 50%;
  background: transparent;
  z-index: 2;
}

.circleFace.no-face {
  border-color: white;
}

.overlayTakePhotoFace::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 0;

  mask: radial-gradient(ellipse 300px 300px at center,
      transparent 50%,
      white 30%,
      white 31%,
      rgba(0, 0, 0, 0.5) 32%);
  -webkit-mask: radial-gradient(ellipse 300px 300px at center,
      transparent 50%,
      white 30%,
      white 31%,
      rgba(0, 0, 0, 0.5) 32%);
}

.overlayTakePhotoRect {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.message {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background-color: rgba(255, 255, 255, 0.7);
  padding: 10px;
  border-radius: 5px;
  font-size: 15px;
  font-weight: bold;
  color: black;
  z-index: 3;
}

.messageDocument {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background-color: rgba(255, 255, 255, 0.7);
  padding: 10px;
  border-radius: 5px;
  font-size: 15px;
  font-weight: bold;
  color: black;
  z-index: 5;
}
</style>
