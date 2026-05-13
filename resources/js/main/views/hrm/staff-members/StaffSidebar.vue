<template>
    <div class="setting-sidebar">
        <perfect-scrollbar
            :options="{
                wheelSpeed: 1,
                swipeEasing: true,
                suppressScrollX: true,
            }"
        >
            <a-menu v-model:selectedKeys="selectedKeys">
                <a-menu-item
                    key="staff"
                    v-if="
                        permsArray.includes('users_view') || permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.hrm.staffs.index' })"
                >
                    <template #icon>
                        <UserOutlined />
                    </template>
                    {{ $t("menu.staff_members") }}
                </a-menu-item>
                <a-menu-item
                    key="departments"
                    v-if="
                        permsArray.includes('departments_view') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.hrm.departments.index' })"
                >
                    <template #icon>
                        <ContainerOutlined />
                    </template>
                    {{ $t("menu.departments") }}
                </a-menu-item>
                <a-menu-item
                    key="designations"
                    v-if="
                        permsArray.includes('designations_view') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.hrm.designations.index' })"
                >
                    <template #icon>
                        <SaveOutlined />
                    </template>
                    {{ $t("menu.designations") }}
                </a-menu-item>
                <a-menu-item
                    key="shifts"
                    v-if="
                        permsArray.includes('shifts_view') || permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.hrm.shifts.index' })"
                >
                    <template #icon>
                        <TrademarkOutlined />
                    </template>
                    {{ $t("menu.shifts") }}
                </a-menu-item>
            </a-menu>
        </perfect-scrollbar>
    </div>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import {
    UserOutlined,
    ContainerOutlined,
    SaveOutlined,
    TrademarkOutlined,
} from "@ant-design/icons-vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";
import common from "../../../../common/composable/common";

export default defineComponent({
    components: {
        UserOutlined,
        ContainerOutlined,
        SaveOutlined,
        TrademarkOutlined,
    },
    setup() {
        const { appSetting, user, permsArray, appModules, appType } = common();
        const route = useRoute();
        const store = useStore();
        const selectedKeys = ref([]);

        onMounted(() => {
            const menuKey =
                typeof route.meta.menuKey == "function"
                    ? route.meta.menuKey(route)
                    : route.meta.menuKey;

            selectedKeys.value = [menuKey.replace("-", "_")];
        });

        watch(route, (newVal, oldVal) => {
            const menuKey =
                typeof newVal.meta.menuKey == "function"
                    ? newVal.meta.menuKey(newVal)
                    : newVal.meta.menuKey;

            selectedKeys.value = [menuKey.replace("-", "_")];
        });

        return {
            permsArray,

            selectedKeys,
            appType,
        };
    },
});
</script>

<style lang="less">
.setting-sidebar .ps {
    height: calc(100vh - 145px);
}
</style>
