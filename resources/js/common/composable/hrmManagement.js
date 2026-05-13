import { ref } from "vue";
import { useI18n } from "vue-i18n";
import { find } from "lodash-es";
import common from "../../common/composable/common";

const hrmManagement = () => {
    const { dayjs } = common();
    const { t } = useI18n();

    const monthArrays = ref([
        { name: t("common.january"), value: "01" },
        { name: t("common.february"), value: "02" },
        { name: t("common.march"), value: "03" },
        { name: t("common.april"), value: "04" },
        { name: t("common.may"), value: "05" },
        { name: t("common.june"), value: "06" },
        { name: t("common.july"), value: "07" },
        { name: t("common.august"), value: "08" },
        { name: t("common.september"), value: "09" },
        { name: t("common.october"), value: "10" },
        { name: t("common.november"), value: "11" },
        { name: t("common.december"), value: "12" },
    ]);

    const weekDays = ref([
        { name: t("common.monday"), value: "Monday" },
        { name: t("common.tuesday"), value: "Tuesday" },
        { name: t("common.wednesday"), value: "Wedensday" },
        { name: t("common.thursday"), value: "Thrusday" },
        { name: t("common.friday"), value: "Friday" },
        { name: t("common.saturday"), value: "Saturday" },
        { name: t("common.sunday"), value: "Sunday" },
    ]);

    const getMonthNameByNumber = (monthNumber) => {
        var newMonthNumber = parseInt(monthNumber);
        newMonthNumber =
            newMonthNumber <= 9 ? "0" + newMonthNumber : "" + newMonthNumber;
        const month = find(monthArrays.value, { value: newMonthNumber });

        return month ? month.name : "-";
    };

    const formatMinutes = (totalMinutes) => {
        if (totalMinutes > 0) {
            var hours = Math.floor(totalMinutes / 60);
            var minutes = totalMinutes % 60;

            var output = "";
            if (hours > 0) {
                output += `${hours} ${t("attendance.hours")} `;
            }

            if (minutes > 0) {
                output += `${minutes} ${t("attendance.minutes")}`;
            }
            return output;
        } else {
            return "-";
        }
    };

    const formatShiftTime24Hours = (shiftTime) => {
        return dayjs(shiftTime, "hh:mm A").format("HH:mm:ss");
    };

    const formatShiftTime12Hours = (shiftTime) => {
        return dayjs(shiftTime, "HH:mm:ss").format("hh:mm A");
    };

    return {
        monthArrays,
        getMonthNameByNumber,
        formatMinutes,
        formatShiftTime24Hours,
        formatShiftTime12Hours,
        weekDays,
    };
};

export default hrmManagement;
