import FnBApp from '../FnBApp';

export default function Page(props: any) {
    return <FnBApp _module="menu-items" _action="create" {...props} />;
}
