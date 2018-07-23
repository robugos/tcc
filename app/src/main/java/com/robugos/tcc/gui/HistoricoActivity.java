package com.robugos.tcc.gui;

import android.app.ProgressDialog;
import android.content.Intent;
import android.net.ParseException;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.Toast;

import com.robugos.tcc.R;
import com.robugos.tcc.dao.HttpHandler;
import com.robugos.tcc.dao.SQLiteHandler;
import com.robugos.tcc.dominio.ListViewAdapter;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.Date;
import java.util.HashMap;

public class HistoricoActivity extends AppCompatActivity {

    ArrayList<HashMap<String, String>> listaEventos;
    private ListView lView;
    private String TAG = HistoricoActivity.class.getSimpleName();
    private static String url = "http://robugos.com/tcc/api/user/historic.php?uid=";
    private ProgressDialog pDialog;
    private  GetEventos loader = new GetEventos();
    ListViewAdapter adapter;
    private static String userId;
    private SQLiteHandler db = new SQLiteHandler(this);
    private static String notauser;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_historico);

        userId = db.getUserDetails().get("uid");
        listaEventos = new ArrayList<>();
        lView = (ListView) findViewById(R.id.listaEventos);

        loader.execute();
    }

    //Classe AsyncTask para pegar jSON chamando HTTP
    class GetEventos extends AsyncTask<Void, Void, Void> {
        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            //Mostra dialog de progresso
            pDialog = new ProgressDialog(HistoricoActivity.this);
            pDialog.setMessage("Aguarde");
            pDialog.setCancelable(false);
            pDialog.show();
        }

        @Override
        protected Void doInBackground(Void... arg0) {

            String[] parts = {};
            HttpHandler sh = new HttpHandler();
            // Faz request a URL e pega a resposta
            String jsonStr = sh.chamaServico(url+userId);
            Log.e(TAG, "Respotas da URL: " + jsonStr);
            if (jsonStr != null){
                try {
                    JSONObject jsonObj = new JSONObject(jsonStr);
                    //Pega Array node do JSON
                    JSONArray eventos = jsonObj.getJSONArray("eventos");

                    for (int i = 0; i < eventos.length(); i++){
                        JSONObject eve = eventos.getJSONObject(i);
                        String id = eve.getString("id");
                        String nome = eve.getString("nome");
                        String data = eve.getString("data");

                        data = (data.substring(8, 10))+"/"+(data.substring(5, 7))+
                                "/"+(data.substring(0, 4))+" às "+(data.substring(11, 13))+"h"+(data.substring(14, 16));
                        String local = eve.getString("local");
                        String nota = eve.getString("nota");
                        String avaliado = eve.getString("avaliado");

                        avaliado = "Avaliado em " + (avaliado.substring(8, 10))+"/"+(avaliado.substring(5, 7))+
                                "/"+(avaliado.substring(0, 4))+" às "+(avaliado.substring(11, 13))+"h"+(avaliado.substring(14, 16));

                        //hashmap temporario
                        HashMap<String, String> evento = new HashMap<>();
                        evento.put("id", id);
                        evento.put("nome", nome);
                        evento.put("data", data);
                        evento.put("local", local);
                        evento.put("nota", nota);
                        evento.put("avaliado", avaliado);


                        listaEventos.add(evento);
                    }

                } catch (final JSONException e) {
                    Log.e(TAG, "Erro do JSON parsing: " + e.getMessage());
                        runOnUiThread(new Runnable() {
                            @Override
                            public void run() {
                                Toast.makeText(getApplicationContext(),
                                        "Erro: " + e.getMessage(), Toast.LENGTH_LONG)
                                        .show();
                            }
                        });
                }
            } else {
                Log.e(TAG, "Sem conexão");
                runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        Toast.makeText(getApplicationContext(),
                                "Sem conexão", Toast.LENGTH_LONG)
                                .show();
                    }
                });
            }

            return null;
        }

        @Override
        protected  void onPostExecute(Void result){
            super.onPostExecute(result);
            if (pDialog.isShowing())
                pDialog.dismiss();
            listaEventos = sortListByData(listaEventos);
            adapter = new ListViewAdapter(HistoricoActivity.this, listaEventos, 2);
            adapter.notifyDataSetChanged();
            lView.setAdapter(adapter);
            registerForContextMenu(lView);
            lView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                    Intent intent = new Intent(HistoricoActivity.this, EventoActivity.class);
                    intent.putExtra("id", listaEventos.get(position).get("id"));
                    startActivityForResult(intent, 1);
                }
            });
        }
    }

    private ArrayList<HashMap<String, String>> sortListByData(ArrayList<HashMap<String, String>> list) {
        Collections.sort(list, new Comparator<HashMap< String,String >>() {
            @Override
            public int compare(HashMap<String, String> lhs, HashMap<String, String> rhs) {
                Date data1 = stringToDate(lhs.get("avaliado").substring(12));
                Date data2 = stringToDate(rhs.get("avaliado").substring(12));
                if(null != lhs.get("data") && null != rhs.get("data")){
                    return data1.compareTo(data2);
                }else if(null != lhs.get("data")){
                    return 1;
                }else{
                    return -1;
                }
            }
        });
        Collections.reverse(list);
        return list;
    }

    public Date stringToDate(String data){
        String dia = data.substring(0,11);
        String hora = data.substring(14,16)+":"+data.substring(17,19);
        data = dia+hora;
        SimpleDateFormat dateFormat = new SimpleDateFormat("dd/MM/yyyy hh:mm");
        Date convertedDate = new Date();
        try {
            convertedDate = dateFormat.parse(data);
        } catch (ParseException e) {
            e.printStackTrace();
        } catch (java.text.ParseException e) {
            e.printStackTrace();
        }
        return convertedDate;
    }

    public void onBackPressed(){
        finish();
    }
}