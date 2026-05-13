<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.dashboard`)" class="p-0" />
        </template>
    </AdminPageHeader>

    <div class="dashboard-page-content-container">
        <div class="mt-30 mb-20">
            <div class="mb-20">
                <a-row :gutter="[15, 15]">
                    <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                        <a-card>
                            <template #title>
                                <ClockCircleOutlined />
                                {{ $t("hrm_dashboard.today_attendance") }}
                            </template>
                            <MarkTodayAttendance />
                        </a-card>
                    </a-col>
                </a-row>
            </div>

            <a-row class="mb-20" :gutter="[15, 15]">
                <a-col
                    v-if="
                        permsArray.includes('admin') ||
                        permsArray.includes('leaves_approve_reject')
                    "
                    :xs="24"
                    :sm="24"
                    :md="24"
                    :lg="12"
                    :xl="12"
                >
                    <PedningLeaves />
                </a-col>
                <a-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12">
                    <TodayAttendance />
                </a-col>
            </a-row>
        </div>
    </div>
</template>

<script>
import { ClockCircleOutlined } from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import MarkTodayAttendance from "./MarkTodayAttendance.vue";
import PedningLeaves from "./PedningLeaves.vue";
import TodayAttendance from "./TodayAttendance.vue";

export default {
    components: {
        ClockCircleOutlined,

        AdminPageHeader,
        MarkTodayAttendance,
        PedningLeaves,
        TodayAttendance,
    },
    setup() {
        const { permsArray } = common();

        return {
            permsArray,
        };
    },
};
</script>
