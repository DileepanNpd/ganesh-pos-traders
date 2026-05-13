<template>
    <a-sub-menu key="hrm">
        <template #title>
            <TeamOutlined />
            <span>{{ $t("menu.hrm") }}</span>
            <a-badge
                v-if="appEnv != 'envato'"
                :count="$t('common.module')"
                size="small"
                :style="{ marginLeft: '5px' }"
            ></a-badge>
        </template>
        <a-menu-item
            @click="
                () => {
                    menuSelected();
                    $router.push({ name: 'admin.hrm.dashboards.index' });
                }
            "
            key="dashboards"
        >
            {{ $t("menu.dashboard") }}
        </a-menu-item>
        <a-menu-item
            v-if="
                (permsArray.includes('users_view') ||
                    permsArray.includes('departments_view') ||
                    permsArray.includes('designations_view') ||
                    permsArray.includes('shifts_view') ||
                    permsArray.includes('admin')) &&
                appModules.includes('StockiflyHrm') == true
            "
            @click="
                () => {
                    menuSelected();
                    $router.push({ name: 'admin.hrm.staffs.index' });
                }
            "
            key="staff"
        >
            <span>{{ $t("menu.staff_members") }}</span>
        </a-menu-item>
        <a-menu-item
            @click="
                () => {
                    menuSelected();
                    $router.push({ name: 'admin.hrm.all-holidays.index' });
                }
            "
            key="holidays"
        >
            {{ $t("menu.holidays") }}
        </a-menu-item>
        <a-menu-item
            @click="
                () => {
                    menuSelected();
                    $router.push({ name: 'admin.hrm.appreciations.index' });
                }
            "
            key="appreciations"
        >
            {{ $t("menu.appreciations") }}
        </a-menu-item>
        <a-menu-item
            @click="
                () => {
                    menuSelected();
                    $router.push({ name: 'admin.hrm.leaves.index' });
                }
            "
            key="leaves"
        >
            {{ $t("menu.leaves") }}
        </a-menu-item>
        <a-menu-item
            @click="
                () => {
                    menuSelected();
                    $router.push({ name: 'admin.hrm.attendance.details' });
                }
            "
            key="attendances"
        >
            {{ $t("menu.attendances") }}
        </a-menu-item>
        <a-menu-item
            @click="
                () => {
                    menuSelected();
                    $router.push({ name: 'admin.hrm.payrolls.index' });
                }
            "
            key="payrolls"
        >
            {{ $t("menu.payrolls") }}
        </a-menu-item>
        <a-menu-item
            v-if="permsArray.includes('hrm_settings') || permsArray.includes('admin')"
            @click="
                () => {
                    menuSelected();
                    $router.push({ name: 'admin.hrm_settings.index' });
                }
            "
            key="hrm_settings"
        >
            {{ $t("menu.hrm_settings") }}
        </a-menu-item>
    </a-sub-menu>
</template>

<script>
import { TeamOutlined } from "@ant-design/icons-vue";
import common from "../../../common/composable/common";

export default {
    components: {
        TeamOutlined,
    },
    setup(props, { emit }) {
        const { permsArray, appModules } = common();
        const appEnv = window.config.app_env;

        const menuSelected = () => {
            emit("menuSelected");
        };

        return {
            permsArray,
            appModules,
            menuSelected,
            appEnv,
        };
    },
};
</script>
