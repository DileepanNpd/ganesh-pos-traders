<template>
    <div class="login-main-container">
        <a-row class="main-container-div">
            <a-col :xs="24" :sm="24" :md="24" :lg="8">
                <a-row class="login-left-div">
                    <a-col
                        :xs="{ span: 20, offset: 2 }"
                        :sm="{ span: 20, offset: 2 }"
                        :md="{ span: 16, offset: 4 }"
                        :lg="{ span: 16, offset: 4 }"
                    >
                        <a-card
                            :title="null"
                            class="login-div"
                            :bordered="innerWidth <= 768 ? true : false"
                        >
                            <a-result
                                v-if="onRequestSend.success"
                                :title="$t('messages.reset_password_massage')"
                            >
                                <template #icon>
                                    <SmileTwoTone />
                                </template>
                                <template #extra>
                                    <a-button @click="goToLogin" type="primary">{{
                                        $t("menu.login")
                                    }}</a-button>
                                </template>
                            </a-result>
                            <a-form v-else layout="vertical">
                                <a-row class="mb-30">
                                    <a-col span="24">
                                        <div class="login-logo">
                                            <img
                                                class="login-img-logo"
                                                :src="globalSetting.light_logo_url"
                                            />
                                        </div>
                                    </a-col>
                                </a-row>

                                <a-alert
                                    v-if="onRequestSend.error != ''"
                                    :message="onRequestSend.error"
                                    type="error"
                                    show-icon
                                    class="mb-20 mt-10"
                                />
                                <!-- <a-alert
                                    v-if="onRequestSend.success"
                                    :message="$t('messages.login_success')"
                                    type="success"
                                    show-icon
                                    class="mb-20 mt-10"
                                /> -->

                                <a-form-item
                                    :label="$t('user.new_password')"
                                    name="new_password"
                                    :help="
                                        rules.new_password
                                            ? rules.new_password.message
                                            : null
                                    "
                                    :validateStatus="rules.new_password ? 'error' : null"
                                >
                                    <a-input-password
                                        v-model:value="credentials.new_password"
                                        @pressEnter="onSubmit"
                                        :placeholder="
                                            $t('common.placeholder_default_text', [
                                                $t('user.new_password'),
                                            ])
                                        "
                                    />
                                </a-form-item>
                                <a-form-item
                                    :label="$t('user.confirm_password')"
                                    name="confirm_password"
                                    :help="
                                        rules.confirm_password
                                            ? rules.confirm_password.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.confirm_password ? 'error' : null
                                    "
                                >
                                    <a-input-password
                                        v-model:value="credentials.confirm_password"
                                        @pressEnter="onSubmit"
                                        :placeholder="
                                            $t('common.placeholder_default_text', [
                                                $t('user.confirm_password'),
                                            ])
                                        "
                                    />
                                </a-form-item>

                                <a-form-item class="mt-30">
                                    <a-button
                                        :loading="loading"
                                        @click="onSubmit"
                                        class="login-btn"
                                        block
                                        type="primary"
                                    >
                                        {{ $t("menu.submit") }}
                                    </a-button>
                                </a-form-item>
                            </a-form>
                        </a-card>
                    </a-col>
                </a-row>
            </a-col>
            <a-col :xs="0" :sm="0" :md="24" :lg="16">
                <div class="right-login-div">
                    <img class="right-image" :src="loginBackground" />
                </div>
            </a-col>
        </a-row>
    </div>
</template>

<script>
import { defineComponent, reactive, ref } from "vue";
import { useStore } from "vuex";
import { useRouter, useRoute } from "vue-router";
import common from "../../../common/composable/common";
import apiAdmin from "../../../common/composable/apiAdmin";
import DemoCredentials from "./DemoCredentials.vue";
import { SmileTwoTone } from "@ant-design/icons-vue";

export default defineComponent({
    components: {
        DemoCredentials,
        SmileTwoTone,
    },
    setup() {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { globalSetting, appType } = common();
        const loginBackground = globalSetting.value.login_image_url;
        const store = useStore();
        const route = useRoute();
        const router = useRouter();
        const credentials = reactive({
            new_password: null,
            confirm_password: null,
            token: route.params.id,
        });
        const onRequestSend = ref({
            error: "",
            success: "",
        });

        const onSubmit = () => {
            onRequestSend.value = {
                error: "",
                success: false,
            };

            addEditRequestAdmin({
                url: "auth/reset-password",
                data: credentials,
                success: (response) => {
                    onRequestSend.value = {
                        error: "",
                        success: true,
                    };
                },
                error: (err) => {
                    onRequestSend.value = {
                        error: err.error.message ? err.error.message : "",
                        success: false,
                    };
                },
            });
        };

        const goToLogin = () => {
            router.push({
                name: "admin.login",
            });
        };

        return {
            loading,
            rules,
            credentials,
            onSubmit,
            onRequestSend,
            globalSetting,
            loginBackground,

            innerWidth: window.innerWidth,
            goToLogin,
        };
    },
});
</script>

<style lang="less">
.login-main-container {
    background: #fff;
    height: 100vh;
}

.main-container-div {
    height: 100%;
}

.login-left-div {
    height: 100%;
    align-items: center;
}

.login-logo {
    text-align: center;
}

.login-img-logo {
    width: 150px;
}

.container-content {
    margin-top: 100px;
}

.login-div {
    border-radius: 10px;
}

.outer-div {
    margin: 0;
}

.right-login-div {
    background: #f8f8ff;
    height: 100%;
    display: flex;
    align-items: center;
}

.right-image {
    width: 100%;
    display: block;
    margin: 0 auto;
    height: calc(100vh);
}
</style>
