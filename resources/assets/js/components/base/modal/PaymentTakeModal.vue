<template>
  <base-page>
    <div class="p-8 sm:p-6">
      <div v-if="!imageData">
        <sw-input-group :label="$t('payments.cameras')" class="mb-4">
          <sw-select
            v-model="deviceSelect"
            :options="devices"
            :searchable="true"
            :tabindex="16"
            :allow-empty="false"
            label="label"
            track-by="deviceId"
          />
        </sw-input-group>
        <div class="camera-container">
          <WebCam ref="webcam" @cameras="onCameras" :deviceId="deviceSelect.deviceId"  />

          <div class="overlayTakePhoto">
            <div class="circle" :class="{ 'no-face': !faceDetected }"></div>
            <div class="message" v-if="!faceDetected">No Face Detected</div>
          </div>
        </div>

        <div class="flex justify-center mt-5">
          <sw-button variant="primary-outline" type="button" @click="closeTakeModal">
            {{ $t("general.cancel") }}
          </sw-button>
          <sw-button type="button" class="mx-4" @click="onCapture">
            <CameraIcon class="w-6 h-6 mr-2" />
            Take
          </sw-button>
        </div>
      </div>

      <div v-if="imageData">
        <img :src="imageData" alt="Foto tomada" />

        <div class="flex justify-center mt-5">
          <sw-button variant="primary-outline" type="button" @click="onCapture">
            <CameraIcon class="w-6 h-6" />
          </sw-button>

          <sw-button type="button" class="mx-4" @click="saveImage">
            <CheckIcon class="w-6 h-6" />
          </sw-button>
        </div>
      </div>
    </div>
  </base-page>
</template>


<script>
import { mapActions } from "vuex";
import { WebCam } from "vue-cam-vision";
import { CameraIcon, CheckIcon } from "@vue-hero-icons/solid";
import 'tracking/build/tracking';
import 'tracking/build/data/face';

export default {
  components: {
    CameraIcon,
    CheckIcon,
    WebCam,
  },

  data() {
    return {
      imageData: null,
      devices: [],
      deviceSelect: {},
      faceDetected: false,
    };
  },

  computed: {},

  mounted() {
    this.initTracking();
  },

  methods: {
    ...mapActions("modal", ["closeModal", "resetModalData"]),

    initTracking() {
      const video = this.$refs.webcam.$refs.video;
      const tracker = new window.tracking.ObjectTracker('face');
      tracker.setInitialScale(4);
      tracker.setStepSize(2);
      tracker.setEdgesDensity(0.1);

      window.tracking.track(video, tracker, { camera: true });

      tracker.on('track', (event) => {
        this.faceDetected = event.data.length > 0;
      });
    },

    async onCapture() {
      const image = await this.$refs.webcam?.capture();
      this.imageData = image;
    },

    onCameras(cameras) {
      this.devices = JSON.parse(JSON.stringify(cameras));
      this.deviceSelect = cameras[0];
    },

    onStop() {
      this.$refs.webcam?.stop();
    },

    saveImage() {
      window.hub.$emit("saveImage", this.imageData);
      this.closeTakeModal();
    },

    closeTakeModal() {
      this.onStop();
      this.closeModal();
      this.resetModalData();
    },
  },
};

</script>
<style>
.camera-container {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden; /* Asegura que el overlayTakePhoto no sobresalga del contenedor */
}

.overlayTakePhoto {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.circle {
  position: relative;
  width: 50%;
  height: 80%;
  border: 4px solid green;
  border-radius: 50%;
  background: transparent;
  z-index: 2; /* Asegura que el círculo esté sobre el overlayTakePhoto */
}

.circle.no-face {
  border-color: white;
}

.overlayTakePhoto::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5); /* Fondo oscuro semitransparente */
  z-index: 0; /* Coloca el fondo oscuro detrás del círculo */

  mask: radial-gradient(ellipse 50% 80% at center, transparent 50%, white 30%, white 31%, rgba(0, 0, 0, 0.5) 32%);
  -webkit-mask: radial-gradient(ellipse 50% 80% at center, transparent 50%, white 30%, white 31%, rgba(0, 0, 0, 0.5) 32%);
}

.message {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background-color: rgba(255, 255, 255, 0.7);
  padding: 10px;
  border-radius: 5px;
  font-size: 18px;
  color: red;
  z-index: 3; /* Asegura que el mensaje esté sobre el overlayTakePhoto */
}
</style>
