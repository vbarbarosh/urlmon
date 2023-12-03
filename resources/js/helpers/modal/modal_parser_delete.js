import ModalParserDelete from '../../components/modals/modal-parser-delete.vue';
import vue_modal from '../../vendor/vue_modal';

function modal_parser_delete(value)
{
    return vue_modal({is: ModalParserDelete, value});
}

export default modal_parser_delete;
