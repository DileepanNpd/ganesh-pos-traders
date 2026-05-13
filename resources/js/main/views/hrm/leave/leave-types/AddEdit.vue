<template>
    <a-modal
        :open="visible"
        :closable="false"
        :centered="true"
        :title="pageTitle"
        @ok="onSubmit"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="18" :sm="18" :md="18" :lg="18">
                    <a-form-item
                        :label="$t('leave_type.name')"
                        name="name"
                        :help="rules.name ? rules.name.message : null"
                        :validateStatus="rules.name ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.name"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('leave_type.name'),
                                ])
                            "
                        />
                    </a-form-item>
                </a-col>
                <a-col :xs="6" :sm="6" :md="6" :lg="6">
                    <a-form-item
                        :label="$t('leave_type.is_paid')"
                        name="is_paid"
                        :help="rules.is_paid ? rules.is_paid.message : null"
                        :validateStatus="rules.is_paid ? 'error' : null"
                    >
                        <a-radio-group
                            v-model:value="formData.is_paid"
                            button-style="solid"
                            size="small"
                        >
                            <a-radio-button :value="1">
                                {{ $t("common.yes") }}
                            </a-radio-button>
                            <a-radio-button :value="0">
                                {{ $t("common.no") }}
                            </a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row>
                <a-col :span="24">
                    <a-form-item
                        :label="$t('leave_type.total_leaves')"
                        name="total_leaves"
                        :help="rules.total_leaves ? rules.total_leaves.message : null"
                        :validateStatus="rules.total_leaves ? 'error' : null"
                        class="required"
                    >
                        <a-input-number
                            v-model:value="formData.total_leaves"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('leave_type.total_leaves'),
                                ])
                            "
                            style="width: 100%"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row>
                <a-col :span="24" v-if="formData.is_paid == 1">
                    <a-form-item
                        :label="$t('leave_type.max_leaves_per_month')"
                        name="max_leaves_per_month"
                        :help="
                            rules.max_leaves_per_month
                                ? rules.max_leaves_per_month.message
                                : null
                        "
                        :validateStatus="rules.max_leaves_per_month ? 'error' : null"
                    >
                        <a-input-number
                            v-model:value="formData.max_leaves_per_month"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('leave_type.max_leaves_per_month'),
                                ])
                            "
                            style="width: 100%"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-button key="submit" type="primary" :loading="loading" @click="onSubmit">
                <template #icon>
                    <SaveOutlined />
                </template>
                {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
            </a-button>
            <a-button key="back" @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-modal>
</template>
<script>
import { defineComponent, onMounted, ref } from "vue";
import { PlusOutlined, LoadingOutlined, SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../../common/composable/apiAdmin";
import common from "../../../../../common/composable/common";

export default defineComponent({
    props: [
        "formData",
        "data",
        "visible",
        "url",
        "addEditType",
        "pageTitle",
        "successMessage",
    ],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        const { appSetting, disabledDate, permsArray } = common();

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: props.formData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            appSetting,
            disabledDate,
            permsArray,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
