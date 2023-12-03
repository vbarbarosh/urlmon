import ModalTargetDelete from '../../components/modals/modal-target-delete.vue';
import vue_modal from '../../vendor/vue_modal';

function modal_target_delete(value)
{
    return vue_modal({is: ModalTargetDelete, value});
}

export default modal_target_delete;
