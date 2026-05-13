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
                            v-if="resetPassword"
                            :title="null"
                            class="login-div"
                            :bordered="innerWidth <= 768 ? true : false"
                        >
                            <a-form layout="vertical">
                                <div class="login-logo">
                                    <img
                                        class="login-img-logo"
                                        :src="globalSetting.light_logo_url"
                                    />
                                </div>
                                <br />
                                <div
                                    style="
                                        text-align: left;
                                        margin-top: 10px;
                                        margin-bottom: 30px;
                                    "
                                >
                                    <div
                                        style="
                                            font-weight: bold;
                                            font-size: 18px;
                                            margin-bottom: 2px;
                                        "
                                    >
                                        {{ $t("user.sign_in") }}
                                    </div>
                                    <div style="margin-bottom: 14px">
                                        {{ $t("messages.please_login_to_your_account") }}
                                    </div>
                                </div>
                                <a-alert
                                    v-if="onRequestSend.error != ''"
                                    :message="onRequestSend.error"
                                    type="error"
                                    show-icon
                                    class="mb-20 mt-10"
                                />
                                <a-alert
                                    v-if="onRequestSend.success"
                                    :message="$t('messages.login_success')"
                                    type="success"
                                    show-icon
                                    class="mb-20 mt-10"
                                />
                                <a-form-item
                                    :label="$t('user.email_phone')"
                                    name="email"
                                    :help="rules.email ? rules.email.message : null"
                                    :validateStatus="rules.email ? 'error' : null"
                                >
                                    <a-input
                                        v-model:value="credentials.email"
                                        @pressEnter="onSubmit"
                                        :placeholder="
                                            $t('common.placeholder_default_text', [
                                                $t('user.email_phone'),
                                            ])
                                        "
                                    />
                                </a-form-item>

                                <a-form-item
                                    :label="$t('user.password')"
                                    name="password"
                                    :help="rules.password ? rules.password.message : null"
                                    :validateStatus="rules.password ? 'error' : null"
                                >
                                    <a-input-password
                                        v-model:value="credentials.password"
                                        @pressEnter="onSubmit"
                                        :placeholder="
                                            $t('common.placeholder_default_text', [
                                                $t('user.password'),
                                            ])
                                        "
                                    />
                                </a-form-item>

                                <a-form-item class="mt-30 mb-0">
                                    <a-button
                                        :loading="loading"
                                        @click="onSubmit"
                                        class="login-btn"
                                        block
                                        type="primary"
                                    >
                                        {{ $t("menu.login") }}
                                    </a-button>
                                </a-form-item>
                                <a-form-item
                                    class="mt-10"
                                    style="text-align: center; font-weight: bold"
                                >
                                    <a :loading="loading" @click="onResetPass">
                                        {{ $t("menu.reset_password") }}
                                    </a>
                                </a-form-item>
                                <a-form-item
                                    v-if="appType === 'saas'"
                                    class="mt-10"
                                    style="text-align: center; font-weight: bold"
                                >
                                    <a-button
                                        style="font-weight: bold"
                                        type="link"
                                        :loading="loading"
                                        @click="
                                            () =>
                                                $router.push({
                                                    name: 'superadmin.register',
                                                })
                                        "
                                    >
                                        {{ $t("front_website.register") }}
                                    </a-button>
                                </a-form-item>
                            </a-form>
                            <DemoCredentials :credentials="credentials" />
                        </a-card>
                        <a-card
                            v-else
                            :title="null"
                            class="login-div"
                            :bordered="innerWidth <= 768 ? true : false"
                        >
                            <a-alert
                                v-if="onResetRequest.success"
                                :message="$t('messages.reset_success')"
                                type="success"
                                show-icon
                                class="mb-20 mt-10"
                            />
                            <a-form layout="vertical" v-else>
                                <div class="login-logo mb-30">
                                    <img
                                        class="login-img-logo"
                                        :src="globalSetting.light_logo_url"
                                    />
                                </div>
                                <br />
                                <a-alert
                                    v-if="onResetRequest.error != ''"
                                    :message="onResetRequest.error"
                                    type="error"
                                    show-icon
                                    class="mb-20 mt-10"
                                    style="margin-top: 40px"
                                />

                                <a-form-item
                                    :label="$t('user.email_phone')"
                                    name="email"
                                    :help="rules.email ? rules.email.message : null"
                                    :validateStatus="rules.email ? 'error' : null"
                                    style="margin-top: 32px"
                                >
                                    <a-input
                                        v-model:value="resetCredential.email"
                                        :placeholder="
                                            $t('common.placeholder_default_text', [
                                                $t('user.email_phone'),
                                            ])
                                        "
                                    />
                                </a-form-item>

                                <a-form-item class="mt-30 mb-0">
                                    <a-button
                                        :loading="loading"
                                        @click="onReset"
                                        class="login-btn"
                                        block
                                        type="primary"
                                    >
                                        {{ $t("menu.reset") }}
                                    </a-button>
                                </a-form-item>
                                <a-form-item class="mt-10">
                                    <a-form-item
                                        style="text-align: center; font-weight: bold"
                                    >
                                        <a :loading="loading" @click="onResetClose">
                                            <ArrowLeftOutlined /> {{ $t("common.back") }}
                                        </a>
                                    </a-form-item>
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
import { ArrowLeftOutlined } from "@ant-design/icons-vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import common from "../../../common/composable/common";
import apiAdmin from "../../../common/composable/apiAdmin";
import DemoCredentials from "./DemoCredentials.vue";

export default defineComponent({
    components: {
        DemoCredentials,
        ArrowLeftOutlined,
    },
    setup() {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { globalSetting, appType } = common();
        const loginBackground = globalSetting.value.login_image_url;
        const store = useStore();
        const router = useRouter();
        const resetPassword = ref(true);
        const resetCredential = reactive({
            email: "",
        });
        const credentials = reactive({
            email: null,
            password: null,
        });
        const onRequestSend = ref({
            error: "",
            success: "",
        });

        const onResetRequest = ref({
            error: "",
            success: "",
        });

        const onResetPass = () => {
            resetErrorMessages();

            resetPassword.value = false;
        };

        const onResetClose = () => {
            resetPassword.value = true;

            resetErrorMessages();
        };

        const resetErrorMessages = () => {
            onRequestSend.value = {
                error: "",
                success: "",
            };
            onResetRequest.value = {
                error: "",
                success: "",
            };
            rules.value = {};
        };

        const onReset = () => {
            addEditRequestAdmin({
                url: "auth/forgot-password",
                data: resetCredential,
                success: (response) => {
                    onResetRequest.value = {
                        error: "",
                        success: true,
                    };
                },
                error: (err) => {
                    onResetRequest.value = {
                        error: err.error.message ? err.error.message : "",
                        success: false,
                    };
                },
            });
        };

        const onSubmit = () => {
            onRequestSend.value = {
                error: "",
                success: false,
            };

            addEditRequestAdmin({
                url: "auth/login",
                data: credentials,
                success: (response) => {
                    const user = response.user;
                    store.commit("auth/updateUser", user);
                    store.commit("auth/updateToken", response.token);
                    store.commit("auth/updateExpires", response.expires_in);
                    store.commit("auth/updateSupperVariable", false);
                    store.commit(
                        "auth/updateVisibleSubscriptionModules",
                        response.visible_subscription_modules
                    );

                    if (appType == "non-saas") {
                        store.dispatch("auth/updateAllWarehouses");
                        store.commit("auth/updateWarehouse", response.user.warehouse);

                        router.push({
                            name: "admin.dashboard.index",
                            params: { success: true },
                        });
                    } else {
                        if (user.is_superadmin && user.user_type == "super_admins") {
                            store.commit("auth/updateApp", response.app);
                            store.commit("auth/updateSupperToken", response.token);
                            store.commit("auth/updateSupperVariable", true);
                            store.commit(
                                "auth/updateEmailVerifiedSetting",
                                response.email_setting_verified
                            );
                            router.push({
                                name: "superadmin.dashboard.index",
                                params: { success: true },
                            });
                        } else {
                            store.commit("auth/updateApp", response.app);
                            store.commit(
                                "auth/updateEmailVerifiedSetting",
                                response.email_setting_verified
                            );
                            store.commit(
                                "auth/updateAddMenus",
                                response.shortcut_menus.credentials
                            );
                            store.dispatch("auth/updateAllWarehouses");
                            store.commit("auth/updateWarehouse", response.user.warehouse);
                            router.push({
                                name: "admin.dashboard.index",
                                params: { success: true },
                            });
                        }
                    }
                },
                error: (err) => {
                    onRequestSend.value = {
                        error: err.error.message ? err.error.message : "",
                        success: false,
                    };
                },
            });
        };

        return {
            appType,
            loading,
            rules,
            credentials,
            onSubmit,
            onRequestSend,
            globalSetting,
            loginBackground,

            innerWidth: window.innerWidth,
            onResetPass,
            onResetClose,
            resetPassword,
            onReset,
            resetCredential,
            onResetRequest,
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
