import { useModules } from './useModules';

export function useHotelFnBLink() {
    const { hasHotel, hasFnB } = useModules();
    const isLinkedMode = hasHotel && hasFnB;

    return {
        isLinkedMode,
        canAttachToRoom: isLinkedMode,
        canViewFolio: hasHotel,
        canCreateRoomService: isLinkedMode,
    };
}
