import {Pagination} from "react-bootstrap";
import React from "react";

export const Paginator = (props) => {
    const page = props?.page || 1;
    const total = props?.total || 1;
    const navigate = props?.navigate || (() => {});

    const items: Array<JSX.Element> = [];
    for(let i = page - 2; i <= page + 2; i++) {
        if(i < 1 || i > (total - 1)) {
            continue;
        }

        items.push(
            <Pagination.Item active={page === i}
                onClick={() => navigate(i)}>{i}</Pagination.Item>
        );
    }

    return <Pagination size="lg">
        <Pagination.First onClick={() => navigate(1)} />
        <Pagination.Prev onClick={() => navigate(page - 1)} />
        {items}
        <Pagination.Next onClick={() => navigate(page + 1)} />
        <Pagination.Last onClick={() => navigate(total - 1)} />
    </Pagination>;
}
