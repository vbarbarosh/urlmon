import ModalLoading from '../../components/modals/modal-loading.vue';
import vue_modal from '../../vendor/vue_modal';

function modal_loading(value)
{
    return vue_modal({is: ModalLoading, value});
}

export default modal_loading;
