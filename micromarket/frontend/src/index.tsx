import React from 'react';
import ReactDOM from 'react-dom';
import 'bootstrap/dist/css/bootstrap.min.css';
import App from './App';
import {RecoilRoot} from "recoil";
import RecoilOutside from "recoil-outside"

ReactDOM.render(
  <React.StrictMode>
      <RecoilRoot>
          <RecoilOutside/>
          <App />
      </RecoilRoot>
  </React.StrictMode>,
  document.getElementById('root')
);
    
