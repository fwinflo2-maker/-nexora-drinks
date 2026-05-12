import React from 'react';
import DrinksApp from '@/pages/drinks/DrinksApp';

export default function Page(props: any) {
    return <DrinksApp _module="cash-deposits" _action="show" {...props} />;
}
