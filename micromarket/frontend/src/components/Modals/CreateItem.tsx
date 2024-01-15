import {Alert, Button, Form, Modal} from "react-bootstrap";
import {useState} from "react";
import {query} from "../../lib/api";
import {IResponseCreate} from "../../lib/entities";

const DataDefaults = {
    name: '', desc: '', image_url: '', price: 0
};

export const CreateItem = (props) => {
    const [error, setError] = useState('');
    const [data, setData] = useState(DataDefaults);

    const close = () => {
        if(props.close) {
            props.close();
        }
    }

    const create = () => {
        query('catalog/create', data).then((r) => {
            if(r.error?.text) {
                setError(r.error.text);
            } else if(r.response) {
                setData(DataDefaults);
                if(props.oncreated) {
                    const response = r.response as IResponseCreate;
                    props.oncreated(response.item_id);
                }
                close();
            }
        });
    };

    const setField = (key, value) => {
        setData(Object.assign(data, {[key]: value}));
    };

    return <Modal show={props?.show}>
        <Modal.Header>
            <Modal.Title>Создать товар</Modal.Title>
        </Modal.Header>
        <Modal.Body>
            {(error?.length > 0) && <Alert variant={"danger"}>
                {error}
            </Alert>}

            <Form>
                <Form.Group className="mb-3">
                    <Form.Label>Название товара</Form.Label>
                    <Form.Control type="text" defaultValue={data?.name}
                                  onChange={(e) => setField('name', e.target.value)} />
                </Form.Group>
                <Form.Group className="mb-3">
                    <Form.Label>Описание товара</Form.Label>
                    <Form.Control as="textarea" defaultValue={data?.desc}
                                  onChange={(e) => setField('desc', e.target.value)} />
                </Form.Group>
                <Form.Group className="mb-3">
                    <Form.Label>Ссылка на картинку</Form.Label>
                    <Form.Control type="text" defaultValue={data?.image_url}
                                  onChange={(e) => setField('image_url', e.target.value)} />
                </Form.Group>
                <Form.Group className="mb-3">
                    <Form.Label>Цена</Form.Label>
                    <Form.Control type="number" defaultValue={data?.price}
                                  onChange={(e) => setField('price', e.target.value)}/>
                </Form.Group>
            </Form>
        </Modal.Body>
        <Modal.Footer>
            <Button variant="secondary" onClick={close}>
                Закрыть
            </Button>
            <Button variant="primary" onClick={create}>
                Создать
            </Button>
        </Modal.Footer>
    </Modal>;
}
