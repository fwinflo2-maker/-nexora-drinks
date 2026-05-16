import React from 'react';
import HotelApp from '@/pages/hotel/HotelApp';

export default function Page(props: any) {
    return <HotelApp _module="room-types" _action="edit" {...props} />;
}
