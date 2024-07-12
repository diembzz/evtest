type IMap<T = any> = {
  [key: string | number]: T
}

type IErrors<T> = {
  [K in keyof T]?: string | IErrors<T[K]>
} & {
  [key: string]: string;
}

interface IResponse<T> {
  success: boolean
  status: number
  data?: T | T[]
  meta?: IMeta
  errors?: IErrors<T>
}

interface IMeta extends IMap {
  pagination?: IPagination
}

interface IPagination {
  page: number
  pages: number
  total: number
  size: number
}

interface IResult<T> {

  meta(): IMeta;

  errors(): IErrors<T>;

  error(attribute: string): string | IErrors<T[keyof T]>;

  status(): number;

  success(): boolean;
}

class Result<T> implements IResult<T> {
  public meta: () => IMeta
  public errors: () => IErrors<T>
  public status: () => number
  public success: () => boolean

  constructor(
    res: IResponse<T>
  ) {
    Object.assign(this, res.data ?? {});

    this.meta = (): IMeta => {
      return res.meta ?? {};
    }

    this.errors = (): IErrors<T> => {
      return res.errors ?? {};
    }

    this.status = (): number => {
      return res.status ?? 0;
    }

    this.success = (): boolean => {
      return res.success ?? false;
    }
  }

  error(attribute: string = ''): string | IErrors<T[keyof T]> {
    return this.errors()[attribute] ?? '';
  }
}

class Results<T> extends Array<T> implements IResult<T> {
  public meta: () => IMeta
  public errors: () => IErrors<T>
  public status: () => number
  public success: () => boolean

  constructor(
    res: IResponse<T>
  ) {
    super();

    this.push(...(res.data as Array<T> ?? []));

    this.meta = (): IMeta => {
      return res.meta ?? {};
    }

    this.errors = (): IErrors<T> => {
      return res.errors ?? {};
    }

    this.status = (): number => {
      return res.status ?? 0;
    }

    this.success = (): boolean => {
      return res.success ?? false;
    }
  }

  static make<T>(res: IResponse<T>): Results<T> {
    return new Results<T>(res);
  }

  error(attribute: string = ''): string | IErrors<T[keyof T]> {
    return this.errors()[attribute] ?? '';
  }
}

export default class Api<T = IMap> {
  public base = 'http://localhost/api/v1';

  constructor(
    public readonly resource: string
  ) {
  }

  public async list(args: IMap = {}): Promise<Results<{id: number, name: string}>> {
    return new Results(
      await this.get(this.resource + '/list', args)
    );
  }

  public async index(args: IMap = {}): Promise<Results<T> & T> {
    return new Results<T>(
      await this.get(this.resource, args)
    ) as Results<T> & T;
  }

  public async show(id: any, args: IMap = {}): Promise<Results<T> & T> {
    return new Result<T>(
      await this.get(this.resource + '/' + Number(id), args)
    ) as Results<T> & T;
  }

  public async store(data: IMap = {}): Promise<Results<T> & T> {
    return new Result<T>(
      await this.post(this.resource, data)
    ) as Results<T> & T;
  }

  public async update(id: any, data: IMap = {}): Promise<Results<T> & T> {
    return new Result<T>(
      await this.patch(this.resource + '/' + Number(id), data)
    ) as Results<T> & T;
  }

  public async save(data: IMap = {}): Promise<Results<T> & T> {
    return data.id ? this.update(data.id, data) : this.store(data);
  }

  public async destroy(id: any, args: IMap = {}): Promise<Result<void>>  {
    return new Result<void>(
      await this.delete(this.resource + '/' + Number(id), args)
    );
  }

  // protected

  protected get(url: string, args: IMap = {}): Promise<IResponse<T | any>> {
    return this.fetch('GET', url + this.query(this.clean(args)));
  }

  protected patch(url: string, data: IMap = {}): Promise<IResponse<T | any>> {
    return this.fetch('PATCH', url, this.clean(data));
  }

  protected post(url: string, data: IMap = {}): Promise<IResponse<T | any>> {
    return this.fetch('POST', url, this.clean(data));
  }

  protected delete(url: string, args: IMap = {}): Promise<IResponse<T | any>> {
    return this.fetch('DELETE', url + this.query(this.clean(args)));
  }

  protected async fetch(method: 'GET' | 'POST' | 'PATCH' | 'DELETE', url: string, data: IMap = {}): Promise<IResponse<any>> {
    const options: any = {
      baseURL: this.base,
      method: method,
    };

    if (['POST', 'PATCH'].includes(method)) {
      options.body = JSON.stringify(data);
    }

    return await $fetch(url, options).then((data) => {
      return data;
    }, (err) => {
      return err.response._data;
    })
  }

  protected query(args: IMap): string {
    if (Object.keys(args).length) {
      return '?' + new URLSearchParams(args).toString();
    }

    return '';
  }

  protected clean(data: IMap): IMap {
    for (let key in data) {
      if (data[key] === undefined) {
        delete data[key];
      }
    }

    return data;
  }
}
