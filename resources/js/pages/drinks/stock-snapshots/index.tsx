import React from 'react';
import DrinksApp from '@/pages/drinks/DrinksApp';

export default function StockSnapshotsIndex(props: any) {
    return <DrinksApp _module="stock-snapshots" _action="index" {...props} />;
}
