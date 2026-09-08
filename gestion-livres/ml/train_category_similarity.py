
import pandas as pd
from sklearn.metrics.pairwise import cosine_similarity

data = pd.read_csv('category_data.csv')

matrice = pd.crosstab(data['user_id'], data['category_id'])

similarites = cosine_similarity(matrice.T)

categories = matrice.columns.tolist()

resultats = []
for i, categorie_id in enumerate(categories):
    scores = list(enumerate(similarites[i]))
    scores = [(j, score) for j, score in scores if j != i]
    scores.sort(key=lambda x: x[1], reverse=True)

    if scores:
        j, meilleur_score = scores[0]
        categorie_proche_id = categories[j]
        resultats.append({
            'category_id': categorie_id,
            'categorie_proche_id': categorie_proche_id,
            'score': round(meilleur_score, 4),
        })

df_resultats = pd.DataFrame(resultats)
df_resultats.to_csv('category_similarities.csv', index=False)

print(f"Termine : similarites calculees pour {len(resultats)} categories")
print(df_resultats)