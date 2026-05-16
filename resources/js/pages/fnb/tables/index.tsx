import FnBApp from '../FnBApp';

export default function Page(props: any) {
    return <FnBApp _module="tables" _action="index" {...props} />;
}
