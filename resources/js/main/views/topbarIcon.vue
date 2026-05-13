<template>
    <template v-if="isSelfClocking">
        <a-button type="link" @click="showModal">
            <ClockCircleOutlined />
            <span>{{ $t("hrm_dashboard.clock_in") }}</span>
        </a-button>
        <a-divider type="vertical" />
    </template>

    <a-modal
        v-model:open="visible"
        :centered="true"
        :footer="null"
        :maskClosable="false"
        :destroyOnClose="true"
    >
        <template #title>
            <ClockCircleOutlined />
            {{ $t("hrm_dashboard.today_attendance") }}
        </template>

        <MarkTodayAttendance />
    </a-modal>
</template>

<script>
import { onMounted, ref, computed } from "vue";
import { ClockCircleOutlined } from "@ant-design/icons-vue";
import { useStore } from "vuex";
import common from "../../../../../../resources/js/common/composable/common";
import MarkTodayAttendance from '../../main/views/hrm/dashboard/MarkTodayAttendance.vue';

export default {
    components: {
        ClockCircleOutlined,
        MarkTodayAttendance,
    },
    setup(props, { emit }) {
        const store = useStore();
        const { permsArray, appModules } = common();
        const visible = ref(false);
        const isSelfClocking = computed(() => store.state.hrm.isSelfClocking);

        onMounted(() => {
            store.dispatch("hrm/updateSelfClocking");
        });

        const showModal = () => {
            visible.value = true;
        };

        return {
            permsArray,
            appModules,
            isSelfClocking,

            visible,
            showModal,
        };
    },
};
</script>
