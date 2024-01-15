import React, {useState} from 'react';
import './App.css';

import {Container, Navbar, Row} from "react-bootstrap";

import {ListItems} from "./components/Screens/ListItems";
import {Item} from "./components/Screens/Item";
import {useRecoilState} from "recoil";
import {LastRequestState, RequestTimeState} from "./lib/store";
import {IItem} from "./lib/entities";

function App() {
    const [time] = useRecoilState(RequestTimeState);
    const [url] = useRecoilState(LastRequestState);

    const [currentItem, setCurrentItem] = useState<IItem | null>();

    return (
        <Container fluid>
            <Navbar bg="light">
                <Container>
                    <Navbar.Brand onClick={() => setCurrentItem(null)}>
                        <img
                            alt="" src="/favicon.ico" width="30" height="30"
                            className="d-inline-block align-top"
                        />{' '}
                        Micro Market
                    </Navbar.Brand>

                    <Navbar.Text>
                        {((url && time) || null) && <>
                            API-запрос <b>{url}</b> ответил за <i>{time} мс</i>
                        </>}
                    </Navbar.Text>
                </Container>
            </Navbar>

            <Row style={{"padding": "2rem"}}>
                {currentItem ?
                    <Item data={currentItem} deselect={() => setCurrentItem(null)} /> :
                    <ListItems open={(d) => setCurrentItem(d)} />}
            </Row>
        </Container>
    );
}

export default App;
