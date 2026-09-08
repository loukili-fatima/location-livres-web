import pandas as pd
import joblib

modele = joblib.load('retard_model.pkl')

data = pd.read_csv('active_rentals_data.csv')

X = data[['taux_retard_precedent', 'category_id', 'jour_semaine_emprunt', 'mois_emprunt', 'nb_locations_actives_ce_jour']]

probabilites = modele.predict_proba(X)[:, 1]

resultats = data[['id']].copy()
resultats['risque_retard'] = (probabilites * 100).round(2)

resultats.to_csv('active_rentals_predictions.csv', index=False)

print(f"Termine : risque de retard calcule pour {len(resultats)} locations actives")