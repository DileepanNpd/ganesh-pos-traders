<template>
    <a-skeleton v-if="pageLoading" active />
    <div v-else>
        <a-result
            v-if="attendanceData.is_on_full_day_leave"
            :title="$t('hrm_dashboard.you_are_on_leave')"
        />
        <a-result
            v-else-if="attendanceData.office_hours_expired"
            :title="$t('hrm_dashboard.office_hours_expired')"
        />
        <div v-else>
            <a-row>
                <a-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                    <a-typography-title :level="5">
                        {{ $t(`hrm_dashboard.current_ip_address`) }}
                    </a-typography-title>
                    <p>{{ ipAddress }}</p>
                </a-col>
                <a-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                    <a-typography-title :level="5">
                        {{ $t(`hrm_dashboard.current_time`) }}
                    </a-typography-title>
                    <p>{{ currentTime }}</p>
                </a-col>
            </a-row>

            <a-row v-if="isSelfClocking" class="mt-30" :style="{ textAlign: 'center' }">
                <a-col :span="24">
                    <a-space>
                        <a-button
                            v-if="showClockInButton"
                            type="primary"
                            @click="markAttendance('clock-in')"
                            :loading="clockInLoading"
                        >
                            <ClockCircleOutlined />
                            {{ $t("hrm_dashboard.clock_in") }}
                        </a-button>
                        <a-button v-else type="primary" :disabled="true">
                            {{ $t("hrm_dashboard.clocked_in") }} :
                            {{ formatTime(clockInDateTime) }}
                        </a-button>

                        <a-button
                            v-if="showClockOutButton"
                            type="primary"
                            @click="markAttendance('clock-out')"
                            :loading="clockOutLoading"
                        >
                            <LogoutOutlined />
                            {{ $t("hrm_dashboard.clock_out") }}
                        </a-button>
                        <a-button v-else type="primary" :disabled="true">
                            {{ $t("hrm_dashboard.clocked_out") }} :
                            {{ formatTime(clockOutDateTime) }}
                        </a-button>
                    </a-space>
                </a-col>
            </a-row>
            <a-row v-else class="mt-30" :style="{ textAlign: 'center' }">
                <a-col :span="24">
                    <a-typography-text type="danger" strong>
                        {{ $t("hrm_dashboard.self_clocking_is_disabled") }}
                    </a-typography-text>
                </a-col>
            </a-row>
        </div>
    </div>
</template>

<script>
import { onMounted, ref, computed } from "vue";
import { ClockCircleOutlined, LogoutOutlined } from "@ant-design/icons-vue";
import { useStore } from "vuex";
import common from "../../../../common/composable/common";

export default {
    components: {
        ClockCircleOutlined,
        LogoutOutlined,
    },
    setup(props, { emit }) {
        const { permsArray, dayjs, formatTime } = common();
        const store = useStore();
        const ipAddress = ref("");
        const currentTime = ref("");
        const attendanceData = ref({});
        const clockInLoading = ref(false);
        const clockOutLoading = ref(false);
        const pageLoading = ref(false);
        const isSelfClocking = computed(() => store.state.hrm.isSelfClocking);
        const showClockInButton = computed(() => store.state.hrm.showClockInButton);
        const showClockOutButton = computed(() => store.state.hrm.showClockOutButton);
        const clockInDateTime = computed(() => store.state.hrm.clockInDateTime);
        const clockOutDateTime = computed(() => store.state.hrm.clockOutDateTime);

        onMounted(() => {
            setInterval(displayTime, 1000);

            pageLoading.value = true;
            axiosAdmin.get("/hrm/today-attendance-details").then((response) => {
                attendanceData.value = response.data;
                ipAddress.value = response.data.ip_address;

                updateHrmStoreData(response);

                pageLoading.value = false;
            });
        });

        const markAttendance = (attendanceType) => {
            if (attendanceType == "clock-in") {
                clockInLoading.value = true;
            } else if (attendanceType == "clock-out") {
                clockOutLoading.value = true;
            }
            axiosAdmin
                .post("/hrm/mark-attendance", { type: attendanceType })
                .then((response) => {
                    ipAddress.value = response.data.ip_address;
                    attendanceData.value = response.data;

                    if (attendanceType == "clock-in") {
                        clockInLoading.value = false;
                    } else if (attendanceType == "clock-out") {
                        clockOutLoading.value = false;
                    }

                    updateHrmStoreData(response);
                });
        };

        const updateHrmStoreData = (response) => {
            store.commit("hrm/updateSelfClocking", response.data.self_clocking);
            store.commit(
                "hrm/updateShowClockInButton",
                response.data.show_clock_in_button
            );
            store.commit(
                "hrm/updateShowClockOutButton",
                response.data.show_clock_out_button
            );
            store.commit("hrm/updateClockInDateTime", response.data.clock_in_date_time);
            store.commit("hrm/updateClockOutDateTime", response.data.clock_out_date_time);
        };

        const displayTime = () => {
            currentTime.value = dayjs().format("hh:mm:ss a");
        };

        return {
            pageLoading,
            attendanceData,
            ipAddress,
            currentTime,
            markAttendance,
            clockInLoading,
            clockOutLoading,
            formatTime,
            isSelfClocking,

            showClockInButton,
            showClockOutButton,
            clockInDateTime,
            clockOutDateTime,
        };
    },
};
</script>
