<template>
    <div v-on:click="click_root" class="fix-f oa flex-row" style="background: rgba(0, 0, 0, 0.25)">
        <div class="ma">
            <div class="w500 ma mv15 p15 mg15 white border bs15">
                <p class="xm">Praesent non suscipit lectus. Integer in lectus quam. Sed sed cursus mi. Maecenas mattis, metus ac volutpat vehicula, lacus felis vulputate est, at euismod tortor augue sed neque. Duis porttitor nisl et sapien efficitur, eget feugiat purus dapibus. Nam mattis aliquam felis, in tempor metus tempus at. In molestie vestibulum dolor ut consectetur.</p>
                <p class="xm">Vestibulum aliquet enim quis sodales tincidunt. Donec vulputate magna diam, id feugiat nisi aliquam in. Cras gravida odio ac metus tincidunt pharetra. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum porttitor laoreet neque vel tincidunt. Integer eu scelerisque purus, quis porta ligula. Donec molestie risus ac malesuada laoreet. Duis eleifend enim tincidunt sagittis efficitur. Nunc maximus libero eget rutrum sollicitudin. Praesent cursus non justo sed auctor. Phasellus nibh nibh, egestas eget leo quis, molestie varius felis. In lobortis porta ultrices. Nunc gravida semper diam, a tincidunt urna porta ut. Proin cursus mi quam, vitae lobortis nunc feugiat eu. Maecenas ut gravida lectus, eget tincidunt ante.</p>
                <div>
                    <button v-on:click="modal.return(true)">Confirm</button>
                    <button v-on:click="modal.return(false)">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const modal_hello = {
        props: ['value'],
        inject: ['modal'],
        methods: {
            click_root: function (event) {
                if (event.currentTarget === event.target) {
                    this.modal.return(false);
                }
            },
        }
    };

    export default modal_hello;
</script>
