import ModalUrlDelete from '../../components/modals/modal-url-delete.vue';
import vue_modal from '../../vendor/vue_modal';

function modal_parser_delete(value)
{
    return vue_modal({is: ModalUrlDelete, value});
}

export default modal_parser_delete;
