import { storeToRefs } from "pinia";
import { useRoleStore } from "@/shared/stores/roleStore";

export function useRoles() {
    const store = useRoleStore();
    const { loading, saving, message, error, validationErrors, roles, permissions } = storeToRefs(store);

    return {
        loading,
        saving,
        message,
        error,
        validationErrors,
        roles,
        permissions,
        fetchRolesAndPermissions: store.fetchRolesAndPermissions,
        createRole: store.createRole,
        updateRole: store.updateRole,
        deleteRole: store.deleteRole,
        resetRoleFeedback: store.resetFeedback,
    };
}
