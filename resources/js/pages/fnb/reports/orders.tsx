import FnBApp from '../FnBApp';

export default function Page(props: any) {
    return <FnBApp _module="reports" _action="orders" {...props} />;
}
