import FnBApp from './FnBApp';

export default function Page(props: any) {
    return <FnBApp _module="dashboard" _action="index" {...props} />;
}
