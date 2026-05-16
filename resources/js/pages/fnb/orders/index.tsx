import FnBApp from '../FnBApp';

export default function Page(props: any) {
    return <FnBApp _module="orders" _action="index" {...props} />;
}
