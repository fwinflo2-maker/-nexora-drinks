import React from "react";
import DrinksApp from "./DrinksApp";

export default function Dashboard(props: any) {
    return (
        <DrinksApp
            _module={props._module || "dashboard"}
            _action={props._action || "index"}
            {...props}
        />
    );
}
