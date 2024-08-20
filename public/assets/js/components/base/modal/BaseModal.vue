<template>
  <div>
    <sw-modal ref="baseModal" :variant="variant">
      <template v-slot:header>
        <div
          class="absolute flex content-center justify-center w-5 cursor-pointer"
          style="top: 20px; right: 15px"
          @click="closeModal"
        >
          <x-icon />
        </div>
        <span>{{ modalTitle }}</span>
      </template>
     <!-- {{component}} -->
      <component :is="component" />
    </sw-modal>
  </div>
</template>

<script>
import { XIcon } from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'
import TaxTypeModal from './TaxTypeModal'
import TaxCategoryModal from './TaxCategoryModal'
import ItemModal from './ItemModal'
import EstimateTemplate from './EstimateTemplate'
import InvoiceTemplate from './InvoiceTemplate'
import CustomerModal from './CustomerModal'
import CategoryModal from './CategoryModal'
import CategoryModalTollFree from './CategoryModalTollFree'
import BackupModal from './BackupModal'
import PaymentMode from './PaymentModeModal'
import IndexCsv from './CustomDestinationsIndexCsvModal'
import ItemUnit from './ItemUnitModal'
import MailTestModal from './MailTestModal'
import SendInvoiceModal from './SendInvoiceModal'
import SendEstimateModal from './SendEstimateModal'
import SendPaymentModal from './SendPaymentModal'
import FileDiskModal from './FileDiskModal'
import SetDefaultDiskModal from './SetDefaultDiskModal'
import CustomFieldModal from './CustomField/Index'
import NoteSelectModal from './NoteModal'
import CreatePackageGroup from './CreatePackageGroup'
import Extension from './CorePbxExtensionModal'
import DID from './CorePbxDIDModal'
import Archived from './ArchivedModal'
import CustomDestinationModal from './CustomDestinationModal'
import CustomDidModal from './CustomDidModal'
import CustomDidImportModal from './CustomDidImportModal'
import DownloadCdrsModal from "./DownloadCdrsModal";
import UpdateExtensionModal from "./UpdateExtensionModal";
import SeeDocumentExpensesModal from "./SeeDocumentExpensesModal";
import SeeDocumentExpensesModal from "./SeeDocumentExpensesModal";


export default {
  components: {
    TaxTypeModal,
    TaxCategoryModal,
    ItemModal,
    EstimateTemplate,
    InvoiceTemplate,
    CustomerModal,
    CategoryModal,
    CategoryModalTollFree,
    BackupModal,
    PaymentMode,
    IndexCsv,
    ItemUnit,
    MailTestModal,
    SendInvoiceModal,
    SendEstimateModal,
    SendPaymentModal,
    XIcon,
    FileDiskModal,
    SetDefaultDiskModal,
    CustomFieldModal,
    NoteSelectModal,
    CreatePackageGroup,
    Extension,
    DID,
    Archived,
    CustomDestinationModal,
    CustomDidModal,
    CustomDidImportModal,
    DownloadCdrsModal,
    UpdateExtensionModal,
    SeeDocumentExpensesModal
  },
  data() {
    return {
      component: '',
    }
  },
  computed: {
    ...mapGetters('modal', [
      'modalActive',
      'modalTitle',
      'componentName',
      'modalSize',
      'modalData',
      'variant',
    ]),
  },
  watch: {
    componentName(component) {
      if (!component) {
        return
      }
      this.component = component
    },
    modalActive(status) {
      if (status) {
        this.$refs.baseModal.open()
        return true
      }
      this.$refs.baseModal.close()
      return false
    },
  },
  methods: {
    ...mapActions('modal', ['openModal', 'closeModal']),
  },
}
</script>
