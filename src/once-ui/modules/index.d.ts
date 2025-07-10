import React from 'react';

declare module '@/once-ui/modules' {
  export interface MetaProps {
    title: string;
    description: string;
    baseURL: string;
    image: string;
    path: string;
  }

  export class Meta {
    static generate(props: MetaProps): any;
  }

  export interface SchemaProps {
    as: string;
    baseURL: string;
    path: string;
    title: string;
    description: string;
    image: string;
    author: {
      name: string;
      url: string;
      image: string;
    };
  }

  export const Schema: React.FC<SchemaProps>;
} 