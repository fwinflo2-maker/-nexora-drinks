export default function StocksRangement() {
    return <div>Rangement</div>;
}

StocksRangement.layout = (props: { currentTeam?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Stocks',
            href: props.currentTeam ? `/${props.currentTeam.slug}/stocks` : '/',
        },
        {
            title: 'Rangement',
            href: props.currentTeam ? `/${props.currentTeam.slug}/stocks/rangement` : '#',
        },
    ],
});
