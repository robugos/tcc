package com.robugos.tcc.dominio;

import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.RatingBar;
import android.widget.TextView;

import com.robugos.tcc.R;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Robson on 26/05/2017.
 */

public class ListViewAdapter extends BaseAdapter {
    private Activity activity;
    private ArrayList<HashMap<String, String>> data;
    private static LayoutInflater inflater = null;
    private int TAG;

//    public ListViewAdapter(Activity a, ArrayList<HashMap<String, String>> d) {
//        activity = a;
//        data = d;
//        inflater = (LayoutInflater)activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
//    }

    public ListViewAdapter(Activity a, ArrayList<HashMap<String, String>> d, int x) {
        activity = a;
        data = d;
        TAG = x;
        inflater = (LayoutInflater)activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
    }

    public int getCount() {
        return data.size();
    }

    public Object getItem(int position) {
        return position;
    }

    public long getItemId(int position) {
        return position;
    }

    public View getView(int position, View convertView, ViewGroup parent) {
        View vi = convertView;
        if(convertView == null) {
            if (TAG == 0) {
                vi = inflater.inflate(R.layout.listview_lista_evento_rec, null);
            } else if (TAG == 1){
                vi = inflater.inflate(R.layout.listview_lista_evento, null);
            } else if (TAG == 2){
                vi = inflater.inflate(R.layout.listview_lista_evento_hist, null);
            }
        }

        TextView nomeEvento = (TextView)vi.findViewById(R.id.nomeEvento);
        TextView dataEvento = (TextView)vi.findViewById(R.id.dataEvento);
        TextView localEvento = (TextView)vi.findViewById(R.id.localEvento);
        RatingBar ratingEvento= (RatingBar) vi.findViewById(R.id.ratingEvento);
        if (TAG == 2){
            TextView avaliadoEvento = (TextView)vi.findViewById(R.id.avaliadoEvento);
            avaliadoEvento.setText(data.get(position).get("avaliado"));
        }

        nomeEvento.setText(data.get(position).get("nome"));
        localEvento.setText(data.get(position).get("local"));
        dataEvento.setText(data.get(position).get("data"));
        try {
            ratingEvento.setRating(Float.parseFloat(data.get(position).get("nota")));
        }catch (Exception e) {

        }
        ratingEvento.setContentDescription(data.get(position).get("nome")+" avaliado com: "+data.get(position).get("nota")+" estrelas");

        return vi;
    }
}
