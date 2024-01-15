import axios from "axios";
import {IResponseCreate, IResponseItem, IResponseItemsList, IResponseOperationResult} from "./entities"
import {promiseSetRecoil} from "recoil-outside"
import {LastRequestState, RequestTimeState} from "./store";

interface IApiResponse {
    response: IResponseItemsList | IResponseItem | IResponseCreate | IResponseOperationResult | object | null
    request_time: number
    error: IApiError | null
}

interface IApiError {
    code: number
    text: string
    data: object | null
}

const Endpoint = process.env.REACT_APP_API_URL || 'http://localhost:8080/';

export async function query(method: string, body: object): Promise<IApiResponse> {
    try {
        const url = Endpoint + method;

        const bodyFormData = new FormData();
        for(const k in body) {
            bodyFormData.append(k, body[k]);
        }

        const response = (await axios({
            method: 'post',
            url: url,
            data: bodyFormData,
            headers: { "Content-Type": "multipart/form-data" },
        }))?.data;

        console.log('API ' + method + ': ', response);

        await promiseSetRecoil(RequestTimeState, response?.request_time)
        await promiseSetRecoil(LastRequestState, url)
        return response as IApiResponse;
    } catch (error) {
        console.warn('API ' + method + ': ', error);
        return error as IApiResponse;
    }
}
