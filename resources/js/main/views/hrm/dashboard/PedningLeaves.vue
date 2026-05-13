<template>
    <a-card
        v-if="
            permsArray.includes('admin') || permsArray.includes('leaves_approve_reject')
        "
        :bodyStyle="{ padding: '0px' }"
    >
        <template #title>
            <BellOutlined />
            {{ $t("hrm_dashboard.pening_approvals") }}
            <a-badge :count="pendingLeaves.length" />
        </template>
        <div class="pending-leave-hrm">
            <perfect-scrollbar
                :options="{
                    wheelSpeed: 1,
                    swipeEasing: true,
                    suppressScrollX: true,
                }"
            >
                <a-list
                    :loading="initLoading"
                    item-layout="horizontal"
                    :data-source="pendingLeaves"
                >
                    <template #renderItem="{ item }">
                        <a-list-item>
                            <template #extra>
                                <a-space>
                                    <a-button
                                        v-if="
                                            item.status == 'pending' &&
                                            (permsArray.includes('admin') ||
                                                permsArray.includes(
                                                    'leaves_approve_reject'
                                                ))
                                        "
                                        @click="approveLeave(item.xid)"
                                        type="primary"
                                    >
                                        <template #icon><CheckOutlined /></template>
                                    </a-button>
                                    <a-button
                                        v-if="
                                            item.status == 'pending' &&
                                            (permsArray.includes('admin') ||
                                                permsArray.includes(
                                                    'leaves_approve_reject'
                                                ))
                                        "
                                        @click="rejectLeave(item.xid)"
                                        type="primary"
                                        danger
                                    >
                                        <template #icon><CloseOutlined /></template>
                                    </a-button>
                                </a-space>
                            </template>
                            <a-skeleton
                                avatar
                                :title="false"
                                :loading="!!item.loading"
                                active
                            >
                                <a-list-item-meta :description="item.reason">
                                    <template #title>
                                        <a-typography-text strong>
                                            {{ item.user.name }}
                                        </a-typography-text>
                                        ({{ formatDate(item.start_date) }} -
                                        {{ formatDate(item.end_date) }})
                                    </template>
                                    <template #avatar>
                                        <a-avatar :src="item.user.profile_image_url" />
                                    </template>
                                </a-list-item-meta>
                            </a-skeleton>
                        </a-list-item>
                    </template>
                </a-list>
            </perfect-scrollbar>
        </div>
        <template #extra>
            <a-button
                class="mt-10"
                type="link"
                @click="$router.push({ name: 'admin.hrm.leaves.index' })"
            >
                {{ $t("common.view_all") }}
                <DoubleRightOutlined />
            </a-button>
        </template>
    </a-card>
</template>

<script>
import { onMounted, ref, createVNode } from "vue";
import {
    CheckOutlined,
    CloseOutlined,
    BellOutlined,
    DoubleRightOutlined,
    ExclamationCircleOutlined,
} from "@ant-design/icons-vue";
import { Modal, notification } from "ant-design-vue";
import { useI18n } from "vue-i18n";
import common from "../../../../common/composable/common";

export default {
    components: {
        CheckOutlined,
        CloseOutlined,
        BellOutlined,
        DoubleRightOutlined,
    },
    setup() {
        const { permsArray, dayjs, formatTime, formatDate } = common();
        const { t } = useI18n();
        const initLoading = ref(false);
        const pendingLeaves = ref([]);

        onMounted(() => {
            refetchDataList();
        });

        const refetchDataList = () => {
            initLoading.value = true;

            axiosAdmin.get("hrm/dashboard/pending-leaves").then((response) => {
                pendingLeaves.value = response.data.pending_leaves;

                initLoading.value = false;
            });
        };

        const approveLeave = (xid) => {
            Modal.confirm({
                title: t("common.approved") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("common.approved_message"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),

                onOk() {
                    return axiosAdmin
                        .post(`leaves/update-status/${xid}`, { status: "approved" })
                        .then((successResponse) => {
                            refetchDataList();
                            // Update Visible Subscription Modules
                            notification.success({
                                message: t("common.success"),
                                description: t(`common.status_changed`),
                                placement: "bottomRight",
                            });
                        })
                        .catch(() => {});
                },
                onCancel() {},
            });
        };

        const rejectLeave = (xid) => {
            Modal.confirm({
                title: t("common.rejected") + "?",
                icon: createVNode(ExclamationCircleOutlined),
                content: t("common.rejected_message"),
                centered: true,
                okText: t("common.yes"),
                okType: "danger",
                cancelText: t("common.no"),

                onOk() {
                    return axiosAdmin
                        .post(`leaves/update-status/${xid}`, { status: "rejected" })
                        .then((successResponse) => {
                            refetchDataList();
                            // Update Visible Subscription Modules
                            notification.success({
                                message: t("common.success"),
                                description: t(`common.status_changed`),
                                placement: "bottomRight",
                            });
                        })
                        .catch(() => {});
                },
                onCancel() {},
            });
        };

        return {
            initLoading,
            pendingLeaves,
            permsArray,
            formatDate,

            approveLeave,
            rejectLeave,
        };
    },
};
</script>

<style lang="less">
.pending-leave-hrm .ps {
    height: 400px;
}
</style>
