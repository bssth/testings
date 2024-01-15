import {Alert, Button, Card, Col, Container, ListGroup, Row, Spinner} from "react-bootstrap";
import React, {useCallback, useEffect, useState} from "react";
import {query} from "../../lib/api";
import {IItem, IResponseItemsList} from "../../lib/entities";
import {CreateItem} from "../Modals/CreateItem";
import {Paginator} from "../Common/Paginator";

export const ListItems = (props) => {
    const [loading, setLoading] = useState(true);
    const [items, setItems] = useState<IResponseItemsList>({pages: 0, count: 0, list: []});

    const [orderBy, setOrderBy] = useState(1);
    const [orderDir, setOrderDir] = useState(true);
    const [page, setPage] = useState(1);

    const [createNew, showCreateNew] = useState(false);
    const [alert, setAlert] = useState<string | null>(null);

    const updateList = useCallback(() => {
        query('catalog/list', {
            order: Number(orderBy),
            order_dir: Number(orderDir),
            page: Number(page)
        }).then((r) => {
            if(r.response && ("list" in r.response)) {
                setItems(r.response);
                setLoading(false);
            }
        });
    }, [orderBy, orderDir, page]);

    useEffect(() => {
        updateList();
    }, [updateList]);

    const openItem = (data: IItem) => {
        if(props?.open) {
            props?.open(data);
        }
    }

    return <>
        <CreateItem show={createNew}
                    close={() => showCreateNew(false)}
                    oncreated={(id) => {
                        setAlert(id > 0 ? 'Товар #'+id+' создан' : 'Ошибка');
                        updateList();
                    }} />

        <Row>
            <Col md={10} lg={10} sm={12}>
                {alert && <Alert variant="info">{alert}</Alert>}

                {loading && <Spinner animation="border" style={{ margin: '0.5rem' }} />}

                {(!loading && !items?.count) && <Alert variant="warning">Товары не найдены</Alert>}

                {items?.count > 0 && <Row>
                    {items.list.map(e => (
                        <Card onClick={() => openItem(e)}
                              style={{ width: '15rem', margin: '0.5rem', cursor: 'pointer' }} key={e.id}>
                            <Card.Img variant="top" src={e.image_url} />
                            <Card.Body>
                                <Card.Title>{e.name}</Card.Title>
                                <Card.Text>
                                    {e.desc?.substr(0, 150)}{(e.desc?.length > 150) && '...'}
                                </Card.Text>
                                <Card.Footer>
                                    {e.price} руб.
                                </Card.Footer>
                            </Card.Body>
                        </Card>
                    ))}
                </Row>}
            </Col>
            <Col>
                <ListGroup defaultActiveKey="main">
                    <ListGroup.Item active>
                        Список товаров
                    </ListGroup.Item>
                    <ListGroup.Item action onClick={() => showCreateNew(true)}>
                        Добавить товар
                    </ListGroup.Item>
                    <ListGroup.Item>
                        <Button variant="outline-primary" onClick={() => setOrderBy(orderBy === 1 ? 2 : 1)}>
                            Сортировка по
                            {orderBy === 1 && ' id'}
                            {orderBy === 2 && ' цене'}
                        </Button>{' '}
                        <Button variant="outline-primary" onClick={() => setOrderDir(!orderDir)}>
                            {orderDir ? '▲' : '▼'}
                        </Button>
                    </ListGroup.Item>
                </ListGroup>
            </Col>
        </Row>

        {(items?.pages >= 1 || null) &&
        <Container>
            <Paginator page={page} total={items?.pages} navigate={(p) => {
                if(p >= 1) {
                    setPage(p)
                }
            }} />
        </Container>
        }
    </>;
}
