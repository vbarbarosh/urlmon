import modal from '../components/modals/modal.vue';
import {createApp} from 'vue';

function vue_modal(props)
{
    const tmp = {is: props.is, value: props.value, vm: null};
    tmp.vm = createApp(modal, tmp);
    return tmp.vm.mount(document.body.appendChild(document.createElement('DIV')));
}

export default vue_modal;
