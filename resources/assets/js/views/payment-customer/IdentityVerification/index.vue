<template>
  <div>
    <sw-card class="mt-3">
      <div>
        <div class="grid grid-cols-12 gap-4 mt-3 text-center">
          <div class="col-span-6 md:col-span-2">
            <span class="text-sm font-bold text-black non-italic">
              {{ $t("payments.customer_name") }}
            </span>
            <p class="mt-2 text-sm font-normal non-italic text-primary-800">
              {{ customer?.name }}
            </p>
          </div>

          <div class="col-span-6 md:col-span-2">
            <span class="text-sm font-bold text-black non-italic">
              {{ $t("payments.date") }}
            </span>
            <p class="mt-2 text-sm font-normal non-italic text-primary-800">
              {{ date }}
            </p>
          </div>

          <div class="col-span-6 md:col-span-3">
            <span class="text-sm font-bold text-black non-italic">
              {{ $t("payments.payment_method") }}
            </span>
            <p class="mt-2 text-sm font-normal non-italic text-primary-800">
              {{ paymentMethod?.name }}
            </p>
          </div>

          <div class="col-span-6 md:col-span-3">
            <span class="text-sm font-bold text-black non-italic">
              {{ $t("payments.Payment_manager") }}
            </span>
            <p class="mt-2 text-sm font-normal non-italic text-primary-800">
              {{ paymentGateway?.name }}
            </p>
          </div>

          <div class="col-span-6 md:col-span-2">
            <span class="text-sm font-bold text-black non-italic">
              {{ $t("payments.bills_credit") }}
            </span>
            <div>
              <p
                v-if="invoiceCredit?.length > 0"
                class="mt-2 text-sm font-normal non-italic text-primary-800"
              >
                {{ $t("payments.invoice_title") }}
              </p>
              <p v-else class="mt-2 text-sm font-normal non-italic text-primary-800">
                {{ $t("payments.credit") }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </sw-card>

    <sw-card class="mt-4 pb-6">
      <div v-if="!isVerificationSuccessful">
        <div class="page-header mb-4">
          <div class="text-xl font-bold mb-2">Capture photos for verification</div>
          <div class="text-sm font-normal">
            Use your mobile device to automatically capture photos of your ID and yourself
            to submit for verification.
          </div>
        </div>

        <div class="tabs mb-5 grid col-span-12 pt-6">
          <div
            v-for="(step, index) in stepsVerificationOptions"
            :key="index"
            class="tab border-b"
          >
            <div class="relative">
              <input
                class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
                type="checkbox"
                :id="'chck' + index"
                @change="toggleTab(index)"
                :checked="index === stepWizard"
              />
              <header
                class="col-span-5 flex justify-between items-center py-3 px-2 cursor-pointer select-none tab-label"
                :class="{ 'bg-gray-200 rounded': index === stepWizard }"
                :for="'chck' + index"
              >
                <div class="flex flex-col">
                  <span class="sw-section-title">{{ step.title }}</span>
                  <!-- error  is_valid_document -->
                  <span
                    v-if="!is_valid_document && step.id === 'ic_front'"
                    class="text-red-500 text-sm"
                  >
                    Document is not valid
                  </span>

                  <!-- error  is_valid_selfie -->
                  <span
                    v-if="!is_valid_selfie && step.id === 'selfie'"
                    class="text-red-500 text-sm"
                  >
                    Selfie is not valid
                  </span>
                </div>

                <BadgeCheckIcon v-if="step.showCheck" class="w-7 h-7 text-green-500" />
              </header>
              <div v-show="index === stepWizard" class="tab-content-customer">
                <div class="p-4 border-l-2 border-r-2 border-b-2 border-gray-200">
                  <ul class="list-disc list-inside">
                    <li
                      v-for="(subtitle, subIndex) in step.subtitles"
                      :key="subIndex"
                      class="text-sm font-normal mb-2"
                    >
                      {{ subtitle }}
                    </li>
                  </ul>

                  <sw-button
                    v-if="stepWizard !== null"
                    size="sm"
                    type="button"
                    class="w-full my-4 md:w-auto"
                    @click="takeImage"
                  >
                    <CameraIcon class="w-4 h-4 mr-2" />
                    Launch Camera
                  </sw-button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div>
          <sw-button
            size="sm"
            variant="primary-outline"
            type="button"
            class="w-full mb-4 md:w-auto hidden md:inline-block mr-4"
            @click="cancelValidate()"
          >
            <div class="flex justify-center">
              <XIcon class="w-4 h-4 mr-2" />
              Cancel
            </div>
          </sw-button>

          <!-- validate  -->
          <sw-button
            size="sm"
            type="button"
            :loading="loading"
            class="w-full mb-4 md:w-auto"
            @click="validateDocumentationMethod"
            v-if="
              dataBase64IcFront !== '' &&
              dataBase64IcBack !== '' &&
              dataBase64Selfie !== ''
            "
          >
            <CloudUploadIcon v-if="!loading" class="w-4 h-4 mr-2" />
            Validate
          </sw-button>

          <sw-button
            size="sm"
            variant="primary-outline"
            type="button"
            class="w-full mb-4 md:w-auto md:hidden"
            @click="cancelValidate()"
          >
            <XIcon class="w-4 h-4 mr-2" />
            Cancel
          </sw-button>
        </div>
      </div>

      <takePhoto
        v-if="isShowTakeModal"
        @close="closeTakeModal"
        @saveImage="saveImageWebcam"
        :typePhoto="validarIdCardStep"
        :isShowTakeModal="isShowTakeModal"
      />

      <div v-if="isVerificationSuccessful">
        <div class="page-header text-center mb-4">
          <div class="text-xl font-bold">Verification Successful</div>
        </div>
        <div class="page-content flex justify-center mb-4">
          <lottie-player
            src="/images/lottie/verification_successful.json"
            background="transparent"
            speed="1"
            style="width: 300px; height: 300px"
            loop
            autoplay
          ></lottie-player>
        </div>

        <!-- button de payment -->
        <div class="page-footer">
          <div class="default-container p-4 mb-4">
            <div class="flex justify-center">
              <sw-button type="button" class="mx-4" @click="goToPayment">
                <CreditCardIcon class="w-4 h-4 mr-2" />
                Pay
              </sw-button>
            </div>
          </div>
        </div>
      </div>
    </sw-card>
  </div>
</template>

<script>
import { mapActions } from "vuex";
import { WebCam } from "vue-cam-vision";
import takePhoto from "./takePhoto.vue";
import {
  CameraIcon,
  XIcon,
  ArrowLeftIcon,
  ArrowRightIcon,
  CloudUploadIcon,
  CreditCardIcon,
  BadgeCheckIcon,
} from "@vue-hero-icons/solid";

export default {
  name: "IdentityVerification",
  components: {
    CameraIcon,
    XIcon,
    ArrowLeftIcon,
    ArrowRightIcon,
    CloudUploadIcon,
    CreditCardIcon,
    BadgeCheckIcon,
    "vue-cam-vision": WebCam,
    takePhoto,
  },
  props: {
    customer: {
      type: Object,
      require: true,
    },
    date: {
      type: String,
      require: true,
    },
    paymentMethod: {
      type: Object,
      require: true,
    },
    paymentGateway: {
      type: Object,
      default: {},
      require: true,
    },
    invoiceCredit: {
      type: Array,
      require: true,
    },
    isVerificationSuccessful: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      loading: false,
      dataBase64IcFront: "",
      dataBase64IcBack: "",
      dataBase64Selfie: "",
      stepWizardOption: ["ic_front", "ic_back", "selfie"],
      stepWizard: 0,
      is_valid_document: true,
      is_valid_selfie: true,
      isShowTakeModal: false,



      // isVerificationSuccessful: false,
      // customer: {
      //   id: 96,
      //   name: "John Doe",
      // },
      // date: "2021-07-01",
      // paymentMethod: {
      //   name: "Credit Card",
      // },

      // paymentGateway: {
      //   name: "Stripe",
      // },
      // invoiceCredit: [],
    };
  },
  computed: {
    stepsVerificationOptions() {
      const optionsShow = [
        {
          id: "ic_front",
          title: "Step 1: Capture front of ID",
          showCheck: false,
          subtitles: [
            "Make sure you're in an area with good lighting",
            "Hold mobile device over ID on a dark and flat surface with the front side facing up",
          ],
        },
        {
          id: "ic_back",
          title: "Step 2: Capture back of ID",
          showCheck: false,
          subtitles: [
            "Make sure you're in an area with good lighting",
            "Hold mobile device over ID on a dark and flat surface with the back side facing up",
          ],
        },
        {
          id: "selfie",
          title: "Step 3: Take a selfie",
          showCheck: false,
          subtitles: [
            "Make sure you're in an area with good lighting",
            "Do not cover any part of your face.",
            "Hold mobile device at eye level.",
            "You may be asked to blink or smile.",
          ],
        },
      ];

      optionsShow.forEach((option, index) => {
        if (this.dataBase64IcFront !== "" && option.id === "ic_front") {
          option.showCheck = true;
        } else if (this.dataBase64IcBack !== "" && option.id === "ic_back") {
          option.showCheck = true;
        } else if (this.dataBase64Selfie !== "" && option.id === "selfie") {
          option.showCheck = true;
        }
      });

      return optionsShow;
    },

    disabledNext() {
      if (this.validarIdCardStep === "ic_front") {
        return this.dataBase64IcFront === "";
      } else if (this.validarIdCardStep === "ic_back") {
        return this.dataBase64IcBack === "";
      } else if (this.validarIdCardStep === "selfie") {
        return this.dataBase64Selfie === "";
      }
    },
    validarIdCardStep() {
      return this.stepWizardOption[this.stepWizard];
    },
    cancelValidate() {
      this.$emit("cancelValidateEvent");
    },
  },
  methods: {
    ...mapActions("modal", ["openModal"]),
    ...mapActions("validateIdentification", ["validateDocumentation"]),

    toggleTab(index) {
      this.stepWizard = this.stepWizard === index ? null : index;
    },
    closeTakeModal() {
      this.isShowTakeModal = false;
    },

    takeImage() {
      this.isShowTakeModal = true;
    },
    saveImageWebcam(image) {
      this.setDataBase64(image);
    },

    getImageInput(event) {
      const file = event.target.files[0];

      if (!file) {
        console.error("No file selected");
        return;
      }

      const reader = new FileReader();
      reader.onloadend = () => {
        this.setDataBase64(reader.result);
        this.clearFileInput();
      };

      reader.readAsDataURL(file);
    },
    setDataBase64(base64) {
      const steps = {
        ic_front: () => {
          this.dataBase64IcFront = base64;
          this.nextStep();
        },
        ic_back: () => {
          this.dataBase64IcBack = base64;
          this.nextStep();
        },
        selfie: () => {
          this.dataBase64Selfie = base64;
          this.stepWizard = null;
        },
      };

      if (steps[this.validarIdCardStep]) {
        steps[this.validarIdCardStep]();
      } else {
        console.error("Invalid step");
      }
    },

    clearFileInput() {
      if (this.$refs.fileInput) {
        this.$refs.fileInput.value = "";
      } else {
        console.error("$refs.fileInput is not defined");
      }
    },

    previousStep() {
      this.stepWizard--;
    },
    nextStep() {
      this.stepWizard++;
    },
    async validateDocumentationMethod() {
      try {
        this.is_valid_document = true;
        this.is_valid_selfie = true;
        this.loading = true;
        const payload = {
          customer_id: this.customer.id,
          ic_front: this.dataBase64IcFront,
          ic_back: this.dataBase64IcBack,
          selfie: this.dataBase64Selfie,
        };
        await this.validateDocumentation(payload);

        // event verificationSuccessful
        this.$emit("verificationSuccessful");
      } catch (error) {
        // const errors = error.response.data.errors;
        this.is_valid_document = error.response.data.is_valid_document;
        this.is_valid_selfie = error.response.data.is_valid_selfie;
      } finally {
        this.loading = false;
      }
    },
    goToPayment() {
      this.$emit("goToPayment");
    },
  },
};
</script>
