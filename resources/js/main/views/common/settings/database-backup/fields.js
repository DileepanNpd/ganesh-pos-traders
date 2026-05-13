import { useI18n } from "vue-i18n";
import { getUrlByAppType } from "../../../../../common/scripts/functions";

const fields = () => {
    const url = getUrlByAppType("database-backups");
    const { t } = useI18n();

    const columns = [
        {
            title: t("database_backup.file"),
            dataIndex: "name",
            sorter:true
        },
        {
            title: t("database_backup.file_size"),
            dataIndex: "size",
            sorter:true
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    return {
        url,
        columns,
    }
}

export default fields;
