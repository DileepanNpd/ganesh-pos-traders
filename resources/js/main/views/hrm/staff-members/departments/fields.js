import { ref } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "departments?fields=id,xid,name";
    const addEditUrl = "departments";
    const { t } = useI18n();
    const hashableColumns = [];

    const initData = {
        name: "",
    };

    const columns = ref([
        {
            title: t("department.name"),
            dataIndex: "name",
        },
    ]);

    const filterableColumns = [
        {
            key: "name",
            value: t("common.name")
        },
    ];

    return {
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns
    }
}

export default fields;
