import { ref } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "designations?fields=id,xid,name";
    const addEditUrl = "designations";
    const { t } = useI18n();

    const initData = {
        name: "",
        description: "",
    };

    const columns = ref([
        {
            title: t("designation.name"),
            dataIndex: "name",
        }
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
        filterableColumns
    }
}

export default fields;
