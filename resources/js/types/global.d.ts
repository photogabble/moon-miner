import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { AxiosInstance } from 'axios';
import { route as ziggyRoute } from 'ziggy-js';
import { PageProps as AppPageProps } from './';

declare global {
    type Lang = (string: string, config?: any) => string;
    type TranslationDictionary = Record<string, Record<string, string> | string>;

    interface Window {
        axios: AxiosInstance;
    }

    var route: typeof ziggyRoute;
    var Translations: TranslationDictionary;

}

declare module 'vue' {
    interface ComponentCustomProperties {
        route: typeof ziggyRoute;
        // __: Lang;
    }
}

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, AppPageProps {}
}
