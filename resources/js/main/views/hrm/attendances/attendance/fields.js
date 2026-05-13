import { ref } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "attendances?fields=id,xid,user_id,x_user_id,reason,leave_type_id,x_leave_type_id,date,clock_in_time,clock_out_time,total_duration,user{id,xid,name},is_late,is_half_day,clock_in_date_time,clock_out_date_time,clock_in_ip_address,clock_out_ip_address";
    const addEditUrl = "attendances";
    const { t } = useI18n();
    const hashableColumns = ["user_id", "leave_type_id"];

    const initData = {
        user_id: undefined,
        leave_type_id: undefined,
        date: undefined,
        clock_in_time: undefined,
        clock_out_time: undefined,
        clock_in_ip_address: "",
        clock_out_ip_address: "",
        is_late: 0,
        is_half_day: 0,
        reason: "",
    };

    const columns = ref([
        {
            title: t("attendance.user_id"),
            dataIndex: "user_id",
        },
        {
            title: t("attendance.date"),
            dataIndex: "date",
        },
        {
            title: t("attendance.clock_in_date_time"),
            dataIndex: "clock_in_date_time",
        },
        {
            title: t("attendance.clock_out_date_time"),
            dataIndex: "clock_out_date_time",
        },
        {
            title: t("attendance.clock_in_ip_address"),
            dataIndex: "clock_in_ip_address",
        },
        {
            title: t("attendance.clock_out_ip_address"),
            dataIndex: "clock_out_ip_address",
        },
        {
            title: t("attendance.total_duration"),
            dataIndex: "total_duration",
        },
        {
            title: t("attendance.is_late"),
            dataIndex: "is_late",
        },
        {
            title: t("common.status"),
            dataIndex: "status",
        },
    ]);

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
        url,
    };
};

export default fields;
