export default function StocksPicking() {
    return <div>Picking</div>;
}

StocksPicking.layout = (props: { currentTeam?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Stocks',
            href: props.currentTeam ? `/${props.currentTeam.slug}/stocks` : '/',
        },
        {
            title: 'Picking',
            href: props.currentTeam ? `/${props.currentTeam.slug}/stocks/picking` : '#',
        },
    ],
});
