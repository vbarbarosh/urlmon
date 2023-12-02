import ModalHello from '../../components/modals/modal-hello.vue';
import vue_modal from '../../vendor/vue_modal';

function modal_hello(value)
{
    return vue_modal({is: ModalHello, value});
}

export default modal_hello;
