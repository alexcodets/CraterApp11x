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
import AddressBillingValidationModal from './AddressBillingValidationModal'
import IncomeWithdrawalCashModal from './IncomeWithdrawalCashModal'
import AvalaraBillingLocationModal from './AvalaraBillingLocationModal'
import AvalaraCategoryExemptionsModal from './AvalaraCategoryExemptionsModal'
import Archived from './ArchivedModal'
import CustomerAddressModal from './CustomerAddressModal'
import TaxAgencyModal from './TaxAgencyModal'
import TaxCategoryModal from './TaxCategoryModal'
import TaxTypeModal from './TaxTypeModal'
import OpenCloseCashRegisterModal from './OpenCloseCashRegisterModal'
import HoldInvoicePdfModal from './HoldInvoicePdfModal'
import SelectCashRegisterModal from './SelectCashRegisterModal'
import ItemModal from './ItemModal'
import ItemCategoryModal from "./ItemCategoryModal"
import EstimateTemplate from './EstimateTemplate'
import InvoiceTemplate from './InvoiceTemplate'
import CustomerModal from './CustomerModal'
import ContactModal from './ContactModal'
import CategoryModal from './CategoryModal'
import CategoryModalTollFree from './CategoryModalTollFree'
import BackupModal from './BackupModal'
import PaymentMode from './PaymentModeModal'
import IndexCsv from './CustomDestinationsIndexCsvModal'
import ItemUnit from './ItemUnitModal'
import SectionModal from './SectionsModal'
import MailTestModal from './MailTestModal'
import SendInvoiceModal from './SendInvoiceModal'
import SendEstimateModal from './SendEstimateModal'
import SendPaymentModal from './SendPaymentModal'
import FileDiskModal from './FileDiskModal'
import SetDefaultDiskModal from './SetDefaultDiskModal'
import CustomFieldModal from './CustomField/Index'
import NoteSelectModal from './NoteModal'
import NoteInvoiceModal from './NoteInvoiceModal'
import HoldInvoicesModal from './HoldInvoicesModal'
import HoldReferenceModal from './HoldReferenceModal'
import CreatePackageGroup from './CreatePackageGroup'
import Extension from './CorePbxExtensionModal'
import DID from './CorePbxDIDModal'
import CustomDestinationModal from './CustomDestinationModal'
import CustomDidModal from './CustomDidModal'
import CustomDidImportModal from './CustomDidImportModal'
import DownloadCdrsModal from "./DownloadCdrsModal";
import UpdateExtensionModal from "./UpdateExtensionModal";
import CreateNoteTicketModal from "./CreateNoteTicketModal";
import SeeDocumentExpensesModal from "./SeeDocumentExpensesModal";
import CustomerAvalaraCategoryExemptionsModal from './CustomerAvalaraCategoryExemptionsModal'
import UpdatePriceExtensionModal from './UpdatePriceExtensionModal'
import UpdatePriceDidModal from './UpdatePriceDidModal'
import Prefix from "./PrefixModal";
import PrefixModify from "./PrefixModifyModal";
import ImportExpenseCsvModal from "./ImportExpenseCsvModal";
import MoneyModal from './MoneyModal'
import TablesModal from './TablesModal'
import AddOrChangePaymentAccountModal from './AddOrChangePaymentAccountModal'
import InvoiceImportModal from './InvoiceImportModal'
import EditModalExtensions from './EditModalExtensions'
import CreateModalExtensions from './CreateModalExtensions'
import UpdateDidModal from './UpdateDidModal'
import CreateDidModal from './CreateDidModal'
import CreateEditPaymentFees from './CreateEditPaymentFees'
import PaymentTakeModal from "./PaymentTakeModal"
import SendEstimateSMSModal from "./SendEstimateSMSModal"
import SendInvoiceSMSModal from "./SendInvoiceSMSModal"
import CreateItemPbxservice from "./CreateItemPbxservice"
import SendLeadSMSModal from "./SendLeadSMSModal"
import SendLeadEmailModal from "./SendLeadEmailModal"

export default {
  components: {
    IncomeWithdrawalCashModal,
    Archived,
    AddressBillingValidationModal,
    AvalaraBillingLocationModal,
    AvalaraCategoryExemptionsModal,
    CustomerAddressModal,
    TaxAgencyModal,
    TaxCategoryModal,
    TaxTypeModal,
    OpenCloseCashRegisterModal,
    HoldInvoicePdfModal,
    HoldReferenceModal,
    SelectCashRegisterModal,
    ItemModal,
    TablesModal,
    ItemCategoryModal,
    EstimateTemplate,
    InvoiceTemplate,
    CustomerModal,
    ContactModal,
    CategoryModal,
    CategoryModalTollFree,
    BackupModal,
    PaymentMode,
    IndexCsv,
    ItemUnit,
    SectionModal,
    MailTestModal,
    SendInvoiceModal,
    SendEstimateModal,
    SendPaymentModal,
    XIcon,
    FileDiskModal,
    SetDefaultDiskModal,
    CustomFieldModal,
    NoteSelectModal,
    NoteInvoiceModal,
    HoldInvoicesModal,
    CreatePackageGroup,
    Extension,
    DID,
    CustomDestinationModal,
    CustomDidModal,
    CustomDidImportModal,
    DownloadCdrsModal,
    UpdateExtensionModal,
    CreateNoteTicketModal,
    SeeDocumentExpensesModal,
    CustomerAvalaraCategoryExemptionsModal,
    UpdatePriceExtensionModal,
    UpdatePriceDidModal,
    Prefix,
    PrefixModify,
    ImportExpenseCsvModal,
    MoneyModal,
    AddOrChangePaymentAccountModal,
    InvoiceImportModal,
    EditModalExtensions,
    UpdateDidModal,
    CreateModalExtensions,
    CreateDidModal,
    CreateEditPaymentFees,
    PaymentTakeModal,
    SendEstimateSMSModal,
    SendInvoiceSMSModal,
    CreateItemPbxservice,
    SendLeadSMSModal,
    SendLeadEmailModal
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
