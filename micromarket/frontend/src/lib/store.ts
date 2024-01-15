import {atom} from "recoil";

export const LastRequestState = atom({
    key: 'request_uri',
    default: '#'
});

export const RequestTimeState = atom({
    key: 'request_time',
    default: 0
});
