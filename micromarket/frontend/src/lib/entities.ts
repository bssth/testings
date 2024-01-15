export interface IResponseItemsList {
    count: number
    list: IItem[]
    pages: number
}

export interface IResponseItem {
    item: IItem
}

export interface IResponseOperationResult {
    result: boolean
}

export interface IItem {
    id: number
    name: string
    desc: string
    price: number
    image_url: string
}

export interface IResponseCreate {
    item_id: number
    result: boolean
}
