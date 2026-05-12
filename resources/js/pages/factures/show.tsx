export default function FacturesShow() {
    return <div>Facture</div>;
}

FacturesShow.layout = (props: { currentTeam?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Factures',
            href: props.currentTeam ? `/${props.currentTeam.slug}/factures` : '/',
        },
        {
            title: 'Facture',
            href: '#',
        },
    ],
});
