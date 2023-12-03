import ModalError from '../../components/modals/modal-error.vue';
import vue_modal from '../../vendor/vue_modal';

function modal_hello(value)
{
    return vue_modal({is: ModalError, value});
}

export default modal_hello;
