import {Alert, Badge, Button, Card, Col, Figure, Form, Row} from "react-bootstrap";
import {useState} from "react";
import {IItem, IResponseOperationResult} from "../../lib/entities";
import {query} from "../../lib/api";

export const Item = (props) => {
    const [error, setError] = useState('');
    const [item, setItem] = useState<IItem>(props?.data)

    const [editMode, setEditMode] = useState(false);
    const [editedData, setEditedData] = useState({});

    const deselect = () => {
        if(props?.deselect) {
            props.deselect();
        }
    };

    const setField = (key, value) => {
        setEditedData(Object.assign(editedData, {[key]: value}));
    };

    const deleteItem = () => {
        query('catalog/delete', {id: item.id}).then((r) => {
            if(r.error?.text) {
                setError(r.error.text);
            } else if(r.response && (r.response as IResponseOperationResult).result) {
                deselect();
            }
        });
    };

    const editItem = () => {
        query('catalog/edit', Object.assign(editedData, {id: item?.id})).then((r) => {
            if(r.error?.text) {
                setError(r.error.text);
            } else if(r.response && (r.response as IResponseOperationResult).result) {
                setItem(Object.assign(item, editedData));
                setEditedData({});
                setEditMode(false);
            }
        });
    }

    return <>
        {(error?.length > 0) && <Alert variant={"danger"}>
            {error}
        </Alert>}

        {(item?.id > 0) && <Col>
            <Row>
                <Col md={4} sm={12}>
                    <Figure>
                        <Figure.Image
                            width={'80%'}
                            alt={item.name} src={item.image_url}
                        />
                    </Figure>
                </Col>
                <Col>
                    {(!editMode || null) && <h3>{item.name}</h3>}
                    {editMode && <Form.Control type="text" defaultValue={item?.name}
                                   onChange={(e) => {
                                       setField('name', e.target.value)
                                   }}/>}

                    <h5>
                        Идентификатор: <Badge bg="secondary">{item.id}</Badge>
                    </h5>
                    <h5>
                        Цена: {(!editMode || null) && <Badge bg="secondary">{item.price} рублей</Badge>}
                    </h5>
                    {editMode && <Form.Control type="number" defaultValue={item?.price}
                                   onChange={
                                       (e) =>
                                           setField('price', e.target.value)
                                   }/>}

                    {editMode && <>
                        <h5>Картинка:</h5>
                        <Form.Control type="text" defaultValue={item?.image_url}
                                      onChange={(e) => {
                                          setField('image_url', e.target.value)
                                      }}/>
                    </>}
                </Col>
            </Row>
            <Card>
                <Card.Body>
                    {editMode ? <Form.Control as="textarea" defaultValue={item?.desc} rows={12}
                                   onChange={
                                       (e) => setField('desc', e.target.value)
                                   }/> : item.desc}
                </Card.Body>
                <Card.Footer>
                    {editMode && <>
                        <Button variant="primary" onClick={() => editItem()}>Сохранить</Button>{' '}
                    </>}

                    <Button variant="primary" onClick={() => setEditMode(!editMode)}>
                        {editMode ? 'Отмена' : 'Редактировать'}
                    </Button>{' '}
                    <Button variant="danger" onClick={() => deleteItem()}>Удалить</Button>
                </Card.Footer>
            </Card>
        </Col>}
    </>
}
