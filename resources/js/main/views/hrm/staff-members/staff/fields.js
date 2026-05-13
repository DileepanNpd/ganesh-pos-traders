import { useI18n } from "vue-i18n";

const fields = () => {
  const { t } = useI18n();
  const staffMemberAddEditUrl = "users";
  const hashableColumns = ["shift_id", "department_id", "designation_id"];

  const staffMemberInitData = {
    warehouse_id: undefined,
    warehouses: [],
    name: "",
    email: "",
    profile_image: undefined,
    profile_image_url: undefined,
    phone: "",
    address: "",
    status: "enabled",
    user_type: "staff_members",
    role_id: undefined,
    password: "",
    department_id: undefined,
    designation_id: undefined,
    shift_id: undefined,
  };

  const columns = [
    {
      title: t("user.name"),
      dataIndex: "name",
      key: "name",
    },
    {
      title: t("user.email"),
      dataIndex: "email",
    },

    {
      title: t("user.department_id"),
      dataIndex: "department_id",
    },
    {
      title: t("user.designation_id"),
      dataIndex: "designation_id",
    },
    {
      title: t("user.shift_id"),
      dataIndex: "shift_id",
    },
    {
      title: t("user.status"),
      dataIndex: "status",
      key: "status",
    },
    {
      title: t("user.created_at"),
      dataIndex: "created_at",
    },
    {
      title: t("common.action"),
      dataIndex: "action",
    },
  ];

  const filterableColumns = [
    {
      key: "users.name",
      value: t("user.name"),
    },
    {
      key: "email",
      value: t("user.email"),
    },
    {
      key: "phone",
      value: t("user.phone"),
    },
  ];

  return {
    staffMemberInitData,
    columns,
    filterableColumns,
    hashableColumns,
    staffMemberAddEditUrl,
  };
};

export default fields;
