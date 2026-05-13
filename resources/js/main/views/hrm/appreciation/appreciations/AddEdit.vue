<template>
    <a-drawer
        :title="pageTitle"
        :width="drawerWidth"
        :open="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('appreciation.user')"
                        name="user_id"
                        :help="rules.user_id ? rules.user_id.message : null"
                        :validateStatus="rules.user_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.user_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('appreciation.user'),
                                    ])
                                "
                                :allowClear="true"
                            >
                                <a-select-option
                                    v-for="user in users"
                                    :key="user.xid"
                                    :value="user.xid"
                                >
                                    {{ user.name }}
                                </a-select-option>
                            </a-select>
                            <StaffAddButton @onAddSuccess="userAdded" />
                        </span>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('appreciation.date')"
                        name="date"
                        :help="rules.date ? rules.date.message : null"
                        :validateStatus="rules.date ? 'error' : null"
                        class="required"
                    >
                        <DateTimePicker
                            :dateTime="formData.date"
                            @dateTimeChanged="
                                (changedDateTime) => (formData.date = changedDateTime)
                            "
                        />
                    </a-form-item>
                </a-col>
            </a-row>
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('appreciation.award')"
                        name="award_id"
                        :help="rules.award_id ? rules.award_id.message : null"
                        :validateStatus="rules.award_id ? 'error' : null"
                        class="required"
                    >
                        <span style="display: flex">
                            <a-select
                                v-model:value="formData.award_id"
                                :placeholder="
                                    $t('common.select_default_text', [
                                        $t('appreciation.award'),
                                    ])
                                "
                                :allowClear="true"
                                @change="changeAward"
                            >
                                <a-select-option
                                    v-for="award in awards"
                                    :key="award.xid"
                                    :value="award.xid"
                                    :award="award"
                                >
                                    {{ award.name }}
                                </a-select-option>
                            </a-select>
                            <AwardAddButton @onAddSuccess="awardAdded" />
                        </span>
                    </a-form-item>
                </a-col>
                <a-col :xs="24" :sm="24" :md="12" :lg="12">
                    <a-form-item
                        :label="$t('appreciation.price_amount')"
                        name="price_amount"
                        :help="rules.price_amount ? rules.price_amount.message : null"
                        :validateStatus="rules.price_amount ? 'error' : null"
                        class="required"
                    >
                        <a-input
                            v-model:value="formData.price_amount"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('appreciation.price_amount'),
                                ])
                            "
                        >
                            <template #addonBefore>
                                {{ appSetting.currency.symbol }}
                            </template>
                        </a-input>
                    </a-form-item>
                </a-col>
            </a-row>
            <FormItemHeading>
                {{ $t("appreciation.price_given") }}
            </FormItemHeading>

            <a-row
                :gutter="16"
                v-for="formField in formFields"
                :key="formField.id"
                style="display: flex; align-items: center"
            >
                <a-col :xs="24" :sm="24" :md="23" :lg="23">
                    <a-form-item
                        :label="$t('appreciation.price_given')"
                        name="price_given"
                    >
                        <a-input
                            v-model:value="formField.price_given"
                            :placeholder="$t('appreciation.price_given_placeholder')"
                        />
                    </a-form-item>
                </a-col>
                <p v-if="rules.value" style="color: red">
                    {{ rules.value.message }}
                </p>
                <a-col :span="1" style="margin-top: 6px">
                    <MinusSquareOutlined @click="removeFormField(formField)" />
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item>
                        <a-button
                            type="dashed"
                            block
                            @click="addFormField()"
                            :disabled="addFormButtonStatus"
                        >
                            <PlusOutlined />
                            {{ $t("appreciation.add_price_given") }}
                        </a-button>
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('appreciation.description')"
                        name="description"
                        :help="rules.description ? rules.description.message : null"
                        :validateStatus="rules.description ? 'error' : null"
                        class="required"
                    >
                        <a-textarea
                            v-model:value="formData.description"
                            :placeholder="
                                $t('common.placeholder_default_text', [
                                    $t('appreciation.description'),
                                ])
                            "
                            :rows="4"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-space>
                <a-button
                    key="submit"
                    type="primary"
                    :loading="loading"
                    @click="onSubmit"
                >
                    <template #icon>
                        <SaveOutlined />
                    </template>
                    {{ addEditType == "add" ? $t("common.create") : $t("common.update") }}
                </a-button>
                <a-button key="back" @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </a-space>
        </template>
    </a-drawer>
</template>
<script>
import { defineComponent, onMounted, ref, computed, watch } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    MinusSquareOutlined,
} from "@ant-design/icons-vue";
import { some, forEach, filter } from "lodash-es";
import apiAdmin from "../../../../../common/composable/apiAdmin";
import common from "../../../../../common/composable/common";
import StaffAddButton from "../../../../../main/views/users/StaffAddButton.vue";
import AwardAddButton from "../awards/AddButton.vue";
import DateTimePicker from "../../../../../common/components/common/calendar/DateTimePicker.vue";
import FormItemHeading from "../../../../../common/components/common/typography/FormItemHeading.vue";

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
        MinusSquareOutlined,
        StaffAddButton,
        AwardAddButton,
        DateTimePicker,
        FormItemHeading,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        const { appSetting, disabledDate, permsArray } = common();
        const users = ref([]);
        const usersUrl = "users?limit=10000";
        const allAwards = ref([]);
        const awards = ref([]);
        const awardUrl = "awards?limit=10000";
        const formFields = ref([
            {
                price_given: "",
            },
        ]);

        const removedPriceGiven = ref([]);

        onMounted(() => {
            const userPromise = axiosAdmin.get(usersUrl);
            const awardPromise = axiosAdmin.get(awardUrl);
            Promise.all([userPromise, awardPromise]).then(
                ([userResponse, awardResponse]) => {
                    users.value = userResponse.data;
                    allAwards.value = awardResponse.data;
                    refetchAwards();
                }
            );
        });

        const changeAward = (value, option) => {
            props.formData.price_amount = option.award.award_price;
        };

        const onSubmit = () => {
            var newFormData = {
                ...props.formData,
                price_given: formFieldFilter(),
                removed_price_given: removedPriceGiven.value,
            };

            addEditRequestAdmin({
                url: props.url,
                data: newFormData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };

        const addFormField = () => {
            formFields.value.push({
                price_given: "",
            });
        };

        const formFieldFilter = () => {
            var newFormField = [];

            forEach(formFields.value, (formField) => {
                if (formField.price_given != "") {
                    newFormField.push(formField);
                }
            });

            return newFormField;
        };

        const addFormButtonStatus = computed(() => {
            if (formFields.value.length == 0) {
                return false;
            } else {
                return (
                    some(formFields.value, { price_given: "" }) ||
                    some(formFields.value, { price_given: null })
                );
            }
        });

        const removeFormField = (item) => {
            let index = formFields.value.indexOf(item);
            if (index !== -1) {
                formFields.value.splice(index, 1);
            }

            if (item.id != "") {
                removedPriceGiven.value.push(item.id);
            }
        };

        const userAdded = () => {
            axiosAdmin.get(usersUrl).then((response) => {
                users.value = response.data;
            });
        };

        const awardAdded = () => {
            axiosAdmin.get(awardUrl).then((response) => {
                allAwards.value = response.data;
                refetchAwards();
            });
            emit("awardAdded");
        };

        const refetchAwards = () => {
            if (props.addEditType == "edit") {
                awards.value = filter(allAwards.value, (newAward) => {
                    return newAward.active || newAward.xid == props.formData.award_id;
                });
            } else {
                awards.value = filter(allAwards.value, { active: 1 });
            }
        };

        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        watch(
            () => props.visible,
            (newVal, oldVal) => {
                if (props.visible) {
                    if (props.addEditType == "edit") {
                        formFields.value = [...props.formData.price_given];
                    } else {
                        formFields.value = [];
                    }
                    refetchAwards();
                }
            }
        );

        return {
            loading,
            rules,
            onClose,
            onSubmit,
            appSetting,
            disabledDate,
            permsArray,

            userAdded,
            users,
            awardAdded,
            awards,
            changeAward,
            formFields,
            removeFormField,
            addFormField,
            addFormButtonStatus,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "45%",
        };
    },
});
</script>
