import React from 'react';
import DrinksApp from '@/pages/drinks/DrinksApp';

export default function StockSnapshotsShow(props: any) {
    return <DrinksApp _module="stock-snapshots" _action="show" {...props} />;
}
