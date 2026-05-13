import { useI18n } from "vue-i18n";
import common from "../../../../../common/composable/common";

const fields = () => {
    const { user } = common();
    const url =
        "leaves?fields=id,xid,user_id,x_user_id,user{id,xid,name},leave_type_id,x_leave_type_id,leaveType{id,xid,name},start_date,end_date,is_half_day,reason,status";
    const addEditUrl = "leaves";
    const { t } = useI18n();
    const hashableColumns = ["leave_type_id", "user_id"];

    const initData = {
        user_id: user.value.xid,
        leave_type_id: undefined,
        start_date: undefined,
        end_date: undefined,
        is_half_day: 0,
        reason: "",
        status: "approved",
        date: undefined,
    };

    const columns = [
        {
            title: t("leave.user_id"),
            dataIndex: "user_id",
        },
        {
            title: t("leave.leave_type"),
            dataIndex: "leave_type_id",
        },
        {
            title: t("leave.start_date"),
            dataIndex: "start_date",
        },
        {
            title: t("leave.end_date"),
            dataIndex: "end_date",
        },
        {
            title: t("leave.is_half_day"),
            dataIndex: "is_half_day",
        },
        {
            title: t("leave.status"),
            dataIndex: "status",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("common.name"),
        },
    ];

    return {
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;
